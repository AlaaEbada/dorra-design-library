# Phase 09 — Indexes & Performance Comparison

## Goal
Use actual baseline plans to add justified indexes and compare on the same dataset.

## In scope (mark only with evidence)
- [ ] Choose and justify indexes from recorded query patterns (test candidates: `(category_id, status, created_at, id)` and `title`); note existing FK/PK indexes.
- [ ] Add indexes via a **new migration**, not by rewriting initial schema migration.
- [ ] Run `SHOW INDEX`, real `EXPLAIN` and optional `EXPLAIN ANALYZE` for **exact same** queries and values used in baseline.
- [ ] Repeat equal number of DB-only timing runs under documented comparable cache/load conditions; show raw values plus summary and percent change only if justified.
- [ ] Explicitly report no improvement/regressions and possible causes (selectivity, optimizer, filesort, LIMIT, COUNT, cache).
- [ ] Document index write/storage trade-offs and 1M-row constraints (deep OFFSET, COUNT, keyset/cursor, dedicated search engine if needs change).

## Out of scope
- Benchmark fabrication, replacing MySQL with external search service.

## Acceptance criteria
- [ ] Before/after evidence reproducible on identical rows and predicates.
- [ ] Documentation explains index choices, non-improvements, and scaling caveats.

## Verification candidates (run only if applicable; never fake output)
- `php artisan migrate`
- `SHOW INDEX FROM designs;`
- `EXPLAIN SELECT ...; (same baseline SQL)`

## Suggested commit
`perf: index design search and compare plans`

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
