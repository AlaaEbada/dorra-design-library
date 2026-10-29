# Phase 07 — Feature Tests

## Goal
Add executable tests on small isolated fixtures (not the 100k dataset).

## In scope (mark only with evidence)
- [x] Use `RefreshDatabase`, test factories, and a dedicated test connection/config; never run bulk seeder per test.
- [x] Verify guest auth redirect; Admin create/update/delete; invalid input does not persist.
- [x] Verify Viewer direct POST/PATCH/DELETE is 403, plus edit/create page access as applicable.
- [x] Verify English and Arabic search, combined category/status filters, no-result state.
- [x] Verify ordering by created_at then id, page size 24, preserved params and clean reset behavior.
- [x] Run the full test suite and save exact output/failures.

## Out of scope
- 100k fixture in tests; unverified claims of coverage.

## Acceptance criteria
- [x] All required feature scenarios exist and pass on fresh testing DB.
- [x] Testing is isolated from sample-data DB.

## Verification candidates (run only if applicable; never fake output)
- `php artisan test`
- `git diff --check`

## Suggested commit
`test: cover library roles CRUD and search`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (fill with real evidence)
- Commands and results:
  - `php artisan test` — passed: 36 tests, 209 assertions, duration 2.25s.
  - `git diff --check` — passed.
- Files changed: this feature-test completion record and `docs/PLAN.md`. Required executable scenarios already existed in `tests/Feature/AuthorizationTest.php`, `tests/Feature/DesignCrudTest.php`, and `tests/Feature/DesignListingTest.php`.
- Known issues / assumptions: `phpunit.xml` forces `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:`; relevant feature classes use `RefreshDatabase`. `SampleDataTest` only invokes the bounded test configuration of its seeder, never the 100,000-row production sample dataset.
- Suggested commit message: `test: cover library roles CRUD and search`
- User commit confirmation / hash: user confirmed; verified `fc3bbe0` (`docs: record verified feature-test coverage`).

## Critical audit follow-up — A05 / A03 (2026-10-29)

Status: **Awaiting Commit**. User explicitly authorized these fixes only; historical completion results above are unchanged.

- [x] Force testing/SQLite `:memory:`/empty DB URL in both PHPUnit environment and server variables, including inherited Windows values.
- [x] Validate effective application/database configuration in `createApplication`, before `RefreshDatabase` executes. Reject non-testing, non-SQLite, file/URL and read/write overrides; expose only the SQLite connection inside the test application.
- [x] Verify actual SQLite connection and empty on-disk filename with `PRAGMA database_list`; test seven unsafe configurations without connecting to them.
- [x] Verify Viewer PATCH returns 403 without mutation, alongside existing POST/PUT/DELETE checks.
- [x] Verify invalid PATCH leaves all stored attributes and row count unchanged.
- [x] Verify 255-character Arabic titles succeed and 256-character titles fail for create/update, without invalid writes.
- [x] Follow the rendered Clear filters link from filtered page 2; verify clean query, first page, total count, 24 items and default ordering restored.
- [x] Run complete isolated suite and Pint.

Verification:
- Preflight: cached config absent; reviewed all database test connections/traits and bounded sample seeder (28 rows, chunks of 7). PHP syntax checks passed.
- With process environment deliberately set to `APP_ENV=local`, `DB_CONNECTION=mysql`, `DB_DATABASE=safety_sentinel_do_not_connect`, and a dummy URL on port 1, `php vendor/phpunit/phpunit/phpunit --filter DatabaseSafetyTest --do-not-cache-result` passed **8 tests / 19 assertions** (0.208s, exit 0). No external connection was made.
- Initial hostile-environment run correctly stopped at the guard (8 setup errors, 0 assertions), revealing inherited `$_SERVER` precedence; PHPUnit server overrides fixed that issue.
- First complete run: 48 passed, 1 failed / 276 assertions; the new no-mutation assertion compared differently ordered attribute keys. Taking the initial snapshot from a fresh database read corrected the test; no application change was needed.
- Final `php artisan test --do-not-cache-result`: **49 passed / 277 assertions, 2.43s, exit 0**. Failed 0; skipped 0.
- `vendor\bin\pint --test`: passed, exit 0. `git diff --check`: passed (existing line-ending warning only).
- Initial restricted PHPUnit invocation could not read vendor/autoload.php; the same checks were then executed with approved runtime access.

Files: phpunit.xml, tests/TestCase.php, tests/Feature/DatabaseSafetyTest.php, tests/Feature/DesignCrudTest.php, tests/Feature/DesignListingTest.php, this spec and docs/PLAN.md. Pre-existing README/Phase 10 edits and the historical audit report were preserved.

A01 assessment only: inspected mysql connection configuration, schema/index migrations, bound prefix SQL and benchmark metadata. Actual Oracle MySQL verification remains **NOT RUN**; SQLite/MariaDB results cannot establish it. The benchmark script reads query-cache metadata, so its metadata collection also needs target-version verification. No server/configuration switch, migration, full seed or benchmark rerun occurred.

Suggested commit: `test: enforce database isolation and close feature coverage gaps`
