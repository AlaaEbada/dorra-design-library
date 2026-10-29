# Phase 08 — Performance Baseline

## Goal
Collect reproducible before-index evidence on 100,000 rows before adding custom performance indexes.

## In scope (mark only with evidence)
- [ ] Confirm count 100,000, show original indexes (including automatic FK index) and record machine/OS/PHP/MySQL/version, DB settings relevant to timings.
- [ ] Capture actual SQL + bindings for category+status sorted newest and prefix-title search, matching application queries; choose fixed realistic params and record matched counts.
- [ ] Capture real `EXPLAIN` and optional supported `EXPLAIN ANALYZE` results for both queries.
- [ ] Run repeated isolated DB query timings (e.g., 10 per query), record method, raw values/median, warm vs cold/cache conditions; exclude Blade/PHP rendering/image load.
- [ ] Save baseline in `docs/performance.md` without altering the dataset or adding extra indexes.

## Out of scope
- Creating/tuning performance indexes (Phase 09), made-up timings.

## Acceptance criteria
- [ ] Actual plans, SQL, bindings, environment and timings recorded.
- [ ] No custom index was added before baseline.

## Verification candidates (run only if applicable; never fake output)
- `SHOW INDEX FROM designs;`
- `EXPLAIN SELECT ...; (replace with recorded real SQL)`
- `SELECT COUNT(*) FROM designs;`

## Suggested commit
`perf: document design query baseline`

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
