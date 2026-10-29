# Dorra — Design Library

Standalone Laravel + Blade design library with local sample data, internal login,
role-based authorization, prefix search, and a separate Admin management page.
Uses standard server-rendered Blade navigation and forms. Search submissions
target the filter section through a URL fragment, so the search bar is visible
above results after page load. All detail/edit routes remain available.

## Verified environment

Observed on 2026-10-29: PHP 8.2.12, Laravel 12.70.2, Composer 2.7.9,
Node 24.13.0, npm 11.7.0, MariaDB 10.4.32, Windows 11.
The assignment specifies MySQL; the actual local verification uses MariaDB
through Laravel's `mysql` driver. Oracle MySQL itself has NOT been tested.
Use the committed `composer.lock`. `composer check-platform-reqs` passes locally.
PHP needs PDO MySQL for the application and PDO SQLite for isolated tests, plus
the extensions checked by Composer.

The Blade pages use local `public/css/library.css` and the bundled SVG directly.
Node/npm and a Vite build are not required to run these pages. The repository
retains the original optional Vite files; frontend build verification is NOT RUN.

## New local setup

Run from the repository directory. Keep an existing `.env` and application key;
the copy/key-generation steps below are for a new installation only.

```powershell
composer install
composer check-platform-reqs
Copy-Item .env.example .env
```

Create an **empty local sample database** in your database client:

```sql
CREATE DATABASE dorra_sample CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edit the untracked `.env` with your local DB host, port, database, username and
password. `.env.example` contains placeholders, not working database credentials.
Keep `APP_ENV=local`; set `APP_URL=http://127.0.0.1:8000` for this run command.
Do not set a conflicting `DB_URL`. Then run:

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate
php artisan db:seed --class=DesignLibrarySeeder
php artisan serve --host=127.0.0.1 --port=8000
```

Open [the local login](http://127.0.0.1:8000/login).
If the port is in use, select another port and update APP_URL accordingly.
The application needs writable `storage` and `bootstrap/cache` directories.
No external service, image upload, queue worker or storage symlink is required.
Avoid the generic `composer setup` shortcut for an existing installation: its
scaffold script generates a key and runs additional frontend setup.

Fresh DB migration/seeding was verified with
`php scripts/verify-fresh-setup.php`: 100000 designs, 12 categories, both accounts
and password hashes valid. This verifies the database setup using installed
dependencies; a separate clean-machine clone and dependency installation were
NOT RUN. The helper creates a new timestamp-named local database and retains it.

## Local sample accounts and permissions

These credentials are public **local demo credentials only**. Never deploy them.

| Role   | Email             | Password        |
| ------ | ----------------- | --------------- |
| Admin  | admin@dorra.test  | DorraSample123! |
| Viewer | viewer@dorra.test | DorraSample123! |

Both roles can search/filter `/library` and view draft or published design
details. Library cards and shared details are read-only. Admin uses
`/admin/designs` (Manage designs link) for create/edit/delete, title-prefix search,
and category/status filters that preserve parameters across pages. The management
page includes a footer and viewport-height layout for short result lists.
Viewer requests to
the management page or write routes are rejected server-side with 403.
Public registration, password recovery, user administration and uploads are
intentionally unavailable. Policies and Form Requests enforce authorization and
validation; Blade visibility is supplementary. Forms retain CSRF protection.

To deliberately restore the two local account passwords/roles:

```powershell
php artisan db:seed --class=SampleAccountSeeder
```

## Sample dataset and reset behavior

`DesignLibrarySeeder` inserts 100000 deterministic designs in SQL batches of
1000 inside one transaction, with 12 categories, Arabic/English titles, 50000
drafts and 50000 published designs. Every design uses the same bundled
`images/design-placeholder.svg`. `created_at` is DATETIME; each seeded date is
shared by two designs. `updated_at` starts NULL until editing.

The seeder accepts an empty design table, skips an existing count of exactly
100000, and refuses any other nonzero count. A count-only skip does not verify
content or repair deleted/edited records. The full seeder also creates the demo
accounts on an empty dataset; a count-only skip leaves accounts untouched.
Run without concurrent writes or concurrent seeders. Default `php artisan
db:seed` also calls this full sample seeder.

Prefer a new empty local database for a fresh reproduction. If you intentionally
want a full reset, the following command **permanently drops every table in the
database selected by `.env`**. Back up data and verify that this is a disposable
local sample database first. Never run it against existing valuable data.

```powershell
php artisan migrate:fresh --seed
```

That destructive reset was NOT executed during final verification. The local
dataset includes one user-approved replacement sample row after a manual delete.
The final audit found its checksum differs from the historical benchmark snapshot;
the current data is not an identical reproduction. See the performance and audit
reports; do not reseed the existing database to recreate historical results.

## Search behavior

The user selected **title prefix** search. Examples on a freshly seeded dataset:

- `Design sample 099` matches `Design sample 099999`.
- `تصميم تجريبي 099` matches `تصميم تجريبي 099998`.
- `sample` does not match `Design sample 099999`; this is not substring search.

Category and status filters combine with AND. Search is bound SQL LIKE with
`ESCAPE '!'`: literal `%`, `_`, and `!` are escaped; backslash remains literal.
Laravel trims leading/trailing input whitespace. Prefix matching has no
whole-word boundary or fuzzy spelling behavior.

The verified title collation is `utf8mb4_unicode_ci` on MariaDB 10.4.32.
Recorded probes: Design/design and Café/Cafe matched; أمل/امل and تَصميم/تصميم did
not. Arabic normalization and diacritic removal are not implemented. Variants
depend on the database collation; SQLite tests alone do not establish MySQL or
MariaDB collation behavior. A dedicated search service would make sense only if
future requirements need fuzzy/full-text/relevance features beyond this scope.

Results use `created_at DESC, id DESC`, 24 per page. `id` is the tie-breaker when
dates match. Filter parameters persist across pages; Clear filters resets the
URL. Count and pagination run in the database; categories are eager-loaded.

## Tests and performance evidence

```powershell
php artisan test
vendor\bin\pint --test
git diff --check
```

Latest verified isolated run: **49 tests passed / 277 assertions** (2.43s),
using `php artisan test --do-not-cache-result`; 0 failed and 0 skipped.
See the [recorded verification evidence](docs/specs/07-feature-tests.md#critical-audit-follow-up--a05--a03-2026-10-29).
`vendor\bin\pint --test` and `git diff --check` passed. The temporary SPA
enhancement and its tests were removed at the user's request.
Feature tests use `RefreshDatabase` and SQLite `:memory:` configured in
`phpunit.xml`, with small factories. Seeder tests use a bounded small subclass,
not the 100000-row fixture. Tests cover authorization, CRUD, validation, Arabic
and English prefix search, wildcard literals, filters, empty results, ordering,
page size and query parameter preservation.

[Performance evidence](docs/performance.md) records actual SQL/bindings,
EXPLAIN, machine/settings, raw repeated timings and limitations. Local medians:

| Query                           | Before indexes | After indexes |
| ------------------------------- | -------------- | ------------- |
| Category + status, newest first | 33.3196 ms     | 0.9214 ms     |
| Title prefix, newest first      | 108.8212 ms    | 2.3891 ms     |

New BTREE indexes: `(category_id, status, created_at, id)` and `title`, delivered
in a separate migration. These are warm local database/fetch measurements,
not HTTP timings or production guarantees. Prefix search still needs filesort.
The report explains storage/write costs and one-million-row constraints such as
deep OFFSET, COUNT overhead and possible cursor pagination.

`php scripts/performance-baseline.php` reruns the fixed read-only measurement on
the configured local 100000-row database. Its category id 13 and prefix parameters
match the historical benchmark, not necessarily a newly seeded database's ids.
`php scripts/verify-index-rollback.php` creates a separate small local database,
verifies rollback/reapplication and FK preservation, and retains it for inspection.
These helpers require permission to CREATE DATABASE when creating verification DBs.

## Handoff, scope and outstanding verification

Actual development effort: approximately **2 hours** (user-reported).

See [execution ledger](docs/PLAN.md) and individual [phase specs](docs/specs/).
Last implementation commit: `0f1457b`.
Latest verified audit/test fixes commit: `2890191`.
Subsequent uncommitted README corrections are not included in either commit.
Outstanding verification limitations below remain documented.
No commits or pushes are made automatically.

Browser access was restored on 2026-10-29. Live checks on port 8000 verified
Viewer Arabic prefix + Branding/Published (21 results), empty results/reset,
English prefix (500 results), page 2 preserving the prefix (24 cards), and Viewer
403 on the Admin route. Admin login, management filters (21 results), loaded
thumbnails, footer and create form were checked. Filter position settled at
approximately 16px below viewport top with computed smooth scroll. No horizontal
overflow at the observed 866x715 viewport. A later narrow-viewport audit found the
library usable, with Admin overflow/confirmation scrolling requiring follow-up.
Live create/update/delete submissions and exhaustive visual testing remain NOT RUN;
write behavior is covered by isolated automated tests. Use a disposable sample
database for live write tests to preserve the performance dataset.

Oracle MySQL verification, clean-machine dependency installation, optional
frontend build, production deployment and exhaustive accessibility/browser
testing remain NOT RUN. Current verification is local MariaDB plus isolated
SQLite tests. No paid service or external search engine is used.

Never commit `.env`, application keys or database credentials. Only the documented
local sample login credentials are intentionally public in this repository.
