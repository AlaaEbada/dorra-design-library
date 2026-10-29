# Phase 07 — Feature Tests

## Goal
Add executable tests on small isolated fixtures (not the 100k dataset).

## In scope (mark only with evidence)
- [ ] Use `RefreshDatabase`, test factories, and a dedicated test connection/config; never run bulk seeder per test.
- [ ] Verify guest auth redirect; Admin create/update/delete; invalid input does not persist.
- [ ] Verify Viewer direct POST/PATCH/DELETE is 403, plus edit/create page access as applicable.
- [ ] Verify English and Arabic search, combined category/status filters, no-result state.
- [ ] Verify ordering by created_at then id, page size 24, preserved params and clean reset behavior.
- [ ] Run the full test suite and save exact output/failures.

## Out of scope
- 100k fixture in tests; unverified claims of coverage.

## Acceptance criteria
- [ ] All required feature scenarios exist and pass on fresh testing DB.
- [ ] Testing is isolated from sample-data DB.

## Verification candidates (run only if applicable; never fake output)
- `php artisan test`
- `git diff --check`

## Suggested commit
`test: cover library roles CRUD and search`

## Execution checklist
- [ ] Inspect current code/repo state and read this spec before edits.
- [ ] Implement only the items above.
- [ ] Run and record relevant real verification; leave unverified requirements unchecked.
- [ ] Update this spec and `docs/PLAN.md` with facts and evidence.
- [ ] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (fill with real evidence)
- Commands and results: NOT RUN
- Files changed: none yet
- Known issues / assumptions: none documented yet
- Suggested commit message: TBD
- User commit confirmation / hash: pending
