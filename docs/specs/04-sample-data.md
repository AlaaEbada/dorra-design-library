# Phase 04 — Sample Data

## Goal
Create a reproducible local 100k-design dataset without memory-heavy seeding.

## In scope (mark only with evidence)
- [x] Seed 10+ categories, two demo users, exactly 100,000 designs, draft/published mix, English and Arabic titles and mixed created_at dates.
- [x] Intentionally repeat some created_at values for tie-breaker tests; reuse ONE local placeholder thumbnail path for every design.
- [x] Insert in bounded chunks (~1,000) with controlled memory; avoid 100k model `create()` calls.
- [x] Make seeding and resetting idempotent or document a safe local-only reset sequence; report actual seeding duration, verify record counts and title samples.

## Out of scope
- Per-record image generation/downloads, production/real data.

## Acceptance criteria
- [x] Fresh local seeded DB contains exactly 100,000 designs and 10+ categories.
- [x] Both accounts exist; both statuses and both title languages exist.
- [x] Document reproducible command and actual time.

## Verification candidates (run only if applicable; never fake output)
- `php artisan db:seed --class=DesignLibrarySeeder`
- `SELECT COUNT(*) FROM designs;`
- `SELECT status, COUNT(*) FROM designs GROUP BY status;`

## Suggested commit
`feat: seed design library sample dataset`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (2026-10-29)
- Status: Awaiting Commit. Phase 05 NOT STARTED; prefix search still needs user confirmation at that phase.
- Created: database/seeders/DesignLibrarySeeder.php; public/images/design-placeholder.svg; tests/Feature/SampleDataTest.php.
- Modified: database/seeders/DatabaseSeeder.php (default full local sample seed); README.md (commands, safety/idempotence behavior, metrics, small-test strategy; corrected malformed title encoding); docs/PLAN.md; this spec; Phase 03 spec (Done after user-confirmed verified commit 5c2518d).
- Implementation: local/testing guard, one transaction, 1,000-row bounded insert batches (100 batches), query log disabled, deterministic content and fixed base date, 12 category IDs only held in memory. No 100k model creation/materialization. Each category has both languages/statuses. One code-native SVG asset, shared by all designs; no downloads/uploads.
- Rerun behavior: count exactly 100,000 means no-op (not full integrity verification); nonzero different count throws before changing rows. Initial run refreshes sample account credentials via existing seeder; completed rerun leaves accounts unchanged. Existing categories reused by name. Not concurrency-safe: use offline with one seeder/no concurrent writers. Separate empty sample DB is the documented safe reproduction path, no destructive reset.
- Preflight: `Get-Location`, `git status --short --branch` clean, `git log -1 --oneline` 5c2518d; `php artisan migrate:status` all six Ran. Bootstrap probe: environment local, database dorra_design_library, server 10.4.32-MariaDB, 0 designs / 0 categories.
- Commands/results:
  - Initial `php artisan test --filter=SampleData`: FAILED, 3 passed / 1 failed; production guard test hit Laravel confirmation prompt before seeder. Changed test to invoke seeder directly under production environment; rerun exit 0, 4 passed / 20 assertions.
  - Initial `php artisan db:seed --class=DesignLibrarySeeder --no-interaction`: FAILED, SQLSTATE 22007 / error 1292, updated_at 2025-04-25 00:00:00 invalid under local Cairo DST TIMESTAMP conversion. Probe confirmed transaction rollback: 0 designs / 0 categories. No destructive cleanup performed.
  - Corrected sample updated_at to NULL (schema permits this; no edits have occurred), leaving deterministic required created_at DATETIME unchanged.
  - Successful same seed command: exit 0, inserted exactly 100000 in 5.241 seconds; peak PHP allocated memory 62.00 MiB as reported by hrtime/memory_get_peak_usage(true). Account sub-seeder reported 417 ms. These are actual command metrics, not SQL benchmark timings.
  - Bootstrap SQL verification on project DB: counts/distributions/title samples below; both sample accounts have expected role and password Hash::check PASS.
  - Independent reproduction: reused previously schema-only dorra_schema_verify_da65ed11 after confirming users/categories/designs all empty. Switched database in-process only, then Artisan::call('db:seed', ['--class'=>'DesignLibrarySeeder','--no-interaction'=>true]); exit 0, 100000 inserted in 5.139 seconds; peak process allocation 56.00 MiB. Both sample accounts PASS and all verification aggregates matched. This database now holds a second sample dataset; original historical schema-probe record predates this run. No .env changes or database drops.
  - Repeat `php artisan db:seed --class=DesignLibrarySeeder --no-interaction`: exit 0, 'Target design count already exists; no data or accounts changed.' Small test independently verifies unchanged rows/account hash.
  - `vendor/bin/pint --dirty`: exit 0, active-phase PHP formatting; subsequent `vendor/bin/pint --dirty --test`: exit 0, passed.
  - `php artisan test`: exit 0, 22 passed / 93 assertions. Bulk seeder not used in feature tests: subclass uses only 28 rows with 7-row batches. Covers distribution, chunk boundaries, safe rerun, partial dataset preservation and production guard; earlier auth/schema tests still pass.
  - `git diff --check`: exit 0, line ending normalization warnings only.

### SQL verification (same outputs on both local sample databases)

```sql
SELECT COUNT(*) total, COUNT(DISTINCT category_id) categories_used,
 COUNT(DISTINCT thumbnail) thumbnails, COUNT(DISTINCT created_at) distinct_dates,
 MIN(created_at) oldest, MAX(created_at) newest FROM designs;
-- 100000 | 12 | 1 | 50000 | 2025-01-01 00:00:00 | 2025-12-14 05:10:00
SELECT status, COUNT(*) total FROM designs GROUP BY status;
-- draft 50000; published 50000
SELECT SUM(title LIKE 'Design sample %') english,
 SUM(title LIKE 'تصميم تجريبي %') arabic FROM designs;
-- 50000 | 50000
SELECT created_at, COUNT(*) total FROM designs GROUP BY created_at
 HAVING COUNT(*) > 1 ORDER BY created_at LIMIT 2;
-- 2025-01-01 00:00:00 | 2; 2025-01-01 00:10:00 | 2
SELECT SUM(CRC32(CONCAT_WS('|',d.title,c.name,d.status,d.thumbnail,d.created_at))) fingerprint
 FROM designs d JOIN categories c ON c.id=d.category_id;
-- 214535012394713 (non-cryptographic aggregate content check, not proof of identity)
SELECT title,status,thumbnail,created_at FROM designs ORDER BY id LIMIT 4;
-- Design sample 000001 | draft | images/design-placeholder.svg | 2025-01-01 00:00:00
-- تصميم تجريبي 000002 | draft | images/design-placeholder.svg | 2025-01-01 00:00:00
-- Design sample 000003 | published | images/design-placeholder.svg | 2025-01-01 00:10:00
-- تصميم تجريبي 000004 | published | images/design-placeholder.svg | 2025-01-01 00:10:00
```

- Category count via Category::count(): 12 in each DB. Both accounts: admin@dorra.test and viewer@dorra.test, expected roles, documented local-only password verified hashed. No secrets printed.
- Limitations: MySQL server itself and browser rendering NOT RUN; live library list does not exist until Phase 05. No performance indexes added; no query benchmarks claimed. Peak memory is process-wide allocation, not incremental dataset memory; no total developer time claimed.
- Suggested commit: `feat: seed design library sample dataset`
- User commit confirmation / hash: pending; no staging/commit/push by agent.
