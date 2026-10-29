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
- User commit confirmation / hash: pending
