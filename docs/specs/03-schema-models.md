# Phase 03 — Schema & Models

## Goal
Create minimal schema and Eloquent relations without premature performance indexes.

## In scope (mark only with evidence)
- [x] Create `categories` and `designs` migrations; `designs` includes ID, title, category_id FK, status draft/published, thumbnail path, timestamps (created_at required).
- [x] Create `Category` and `Design` models, proper fillable/casts, `Design belongsTo Category` and `Category hasMany Designs`.
- [x] Create factories for small test fixtures and sample-data command reuse.
- [x] Ensure status constraints/allowed values are enforced in validation (and DB constraint if database compatibility permits).
- [x] Inspect existing indexes with `SHOW INDEX FROM designs`; document PK/FK indexes MySQL creates automatically.

## Out of scope
- Extra title/composite performance indexes (Phase 09 only).
- Soft deletes, audit logs, uploads, category CRUD.

## Acceptance criteria
- [x] Migrations run on a fresh local DB.
- [x] Foreign key and relationships behave correctly.
- [x] No deliberate extra performance indexes exist before baseline.

## Verification candidates (run only if applicable; never fake output)
- `php artisan migrate:status`
- `php artisan migrate`
- `SHOW INDEX FROM designs;`

## Suggested commit
`feat: add design and category schema`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (2026-10-29)
- Status: Awaiting Commit; Phase 04 NOT STARTED.
- Created: app/Enums/DesignStatus.php; app/Models/Category.php; app/Models/Design.php; database/factories/CategoryFactory.php; database/factories/DesignFactory.php; database/migrations/2026_10_29_000001_create_categories_table.php; database/migrations/2026_10_29_000002_create_designs_table.php; tests/Feature/DesignSchemaTest.php.
- Updated: docs/PLAN.md and this spec. Phase 02 spec updated to Done after explicit user confirmation of verified commit 8ae4c4c. Previous documentation corrections preserved.
- Decisions: categories.name is required (not unique); designs.title/category_id/status/thumbnail/created_at are required; default status draft; created_at defaults to current time; category FK restricts deletion. No soft deletes, extra performance indexes or category CRUD.
- Status validation at this layer: backed enum cast rejects invalid model assignments (ValueError); DB enum rejects invalid direct SQL values with Laravel strict connection. HTTP Form Request validation belongs to Phase 06, since no design write endpoints exist yet.
- Factories: small standalone category/design fixtures, shared categories via for($category), published() state; default thumbnail path images/design-placeholder.svg. Actual reusable thumbnail asset and bulk sample generation belong to Phase 04; no upload pipeline added.
- Environment rechecked: `Get-Command php,composer,git`, `php -v` (8.2.12), `php artisan --version` (12.70.2). Existing local server probe returned 10.4.32-MariaDB. MySQL itself NOT RUN.
- Commands/results:
  - `php artisan test --filter=DesignSchema`: exit 0, 7 passed / 13 assertions on isolated SQLite :memory: database. Includes enum rejection, raw SQL constraints, FK restriction, relationship traversal, Arabic title round trip, defaults and timestamps.
  - PHP stdin probes: bootstrapped console Kernel; used existing local mysql connection to CREATE DATABASE with unique generated sample-only name, switched in-process config to that database, purged connection and called Artisan::call('migrate', ['--force'=>true, '--no-interaction'=>true]). No .env changes and no database resets/drops.
  - First fresh DB dorra_schema_verify_aaf72f49: all six migrations ran, PK/FK indexes only. Probe exposed MariaDB TIMESTAMP behavior accepting explicit NULL as current time. This was an unexpected failure of the required-date invariant despite SQLite test passing. Changed the not-yet-applied project migration to DATETIME NOT NULL before running it on the project's local DB.
  - Final fresh DB dorra_schema_verify_da65ed11: exit 0; all six migrations ran from empty schema. Relationships and Arabic/English title round trip PASS; default draft/date PASS; invalid status rejected (SQLSTATE 01000), invalid FK/null created_at/category deletion rejected (SQLSTATE 23000). Probe inserts were transactionally rolled back; design/category counts 0/0. Both isolated verification databases remain locally, with schema only; no destructive cleanup attempted.
  - `php artisan migrate --no-interaction`: exit 0, only the two new migrations applied to project's existing local DB (batch 3).
  - `php artisan migrate:status`: exit 0, all six migrations Ran.
  - `SHOW INDEX FROM designs` via DB::select on both final isolated DB and project DB: exactly PRIMARY(id), unique BTREE; designs_category_id_foreign(category_id), nonunique BTREE. FK index is automatically required/created for InnoDB relationship integrity; no title/status/date/composite performance indexes.
  - Local count probe: 0 designs, 0 categories. No bulk seeding run.
  - `vendor/bin/pint --dirty`: exit 0, formatted new active-phase PHP files; `vendor/bin/pint --dirty --test`: exit 0, passed.
  - `php artisan test`: exit 0, 18 passed / 73 assertions, including all existing auth tests.
  - `git diff --check`: exit 0; line ending normalization warnings only.
- Actual final SHOW CREATE TABLE designs (MariaDB output, whitespace normalized):

```sql
CREATE TABLE `designs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `thumbnail` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designs_category_id_foreign` (`category_id`),
  CONSTRAINT `designs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

- Limitations: no HTTP CRUD/status screens yet; no performance measurement or MySQL-specific compatibility claims. No UI changes in this schema phase; requested visual improvements remain for the UI phase.
- Suggested commit message: `feat: add design and category schema`
- User commit confirmation / hash: pending. Agent did not stage, commit or push.
