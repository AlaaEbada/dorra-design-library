# Phase 08 — Performance Baseline

## Goal
Collect reproducible before-index evidence on 100,000 rows before adding custom performance indexes.

## In scope (mark only with evidence)
- [x] Confirm count 100,000, show original indexes (including automatic FK index) and record machine/OS/PHP/MySQL/version, DB settings relevant to timings.
- [x] Capture actual SQL + bindings for category+status sorted newest and prefix-title search, matching application queries; choose fixed realistic params and record matched counts.
- [x] Capture real `EXPLAIN` and optional supported `EXPLAIN ANALYZE` results for both queries.
- [x] Run repeated isolated DB query timings (e.g., 10 per query), record method, raw values/median, warm vs cold/cache conditions; exclude Blade/PHP rendering/image load.
- [x] Save baseline in `docs/performance.md` without altering the dataset or adding extra indexes.

## Out of scope
- Creating/tuning performance indexes (Phase 09), made-up timings.

## Acceptance criteria
- [x] Actual plans, SQL, bindings, environment and timings recorded.
- [x] No custom index was added before baseline.

## Verification candidates (run only if applicable; never fake output)
- `SHOW INDEX FROM designs;`
- `EXPLAIN SELECT ...; (replace with recorded real SQL)`
- `SELECT COUNT(*) FROM designs;`

## Suggested commit
`perf: document design query baseline`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (fill with real evidence)
- Commands and results: `php scripts/performance-baseline.php` passed; exact count 100000, original PK/FK indexes only, SQL/bindings and EXPLAIN captured; ten timed runs per query. Category/status median 33.3196 ms; prefix median 108.8212 ms. Raw measurements and environment are in `docs/performance.md`. `php -l scripts/performance-baseline.php`, Pint for the script, and `git diff --check` passed.
- Files changed: `scripts/performance-baseline.php`, `docs/performance.md`, this spec, `docs/PLAN.md`, and Phase 07 commit confirmation.
- Known issues / assumptions: user-approved replacement sample row 133001 restored count to 100000 before measurement; dataset checksum 3289254657 must be preserved for Phase 09. Warm local measurements include PDO fetch overhead; no controlled cold-cache benchmark. Initial shell quoting and SHOW metadata failures are recorded in the report. Status: Awaiting Commit.
- Suggested commit message: `perf: document design query baseline`
- User commit confirmation / hash: pending
