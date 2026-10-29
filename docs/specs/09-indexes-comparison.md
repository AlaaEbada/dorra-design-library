# Phase 09 — Indexes & Performance Comparison

## Goal
Use actual baseline plans to add justified indexes and compare on the same dataset.

## In scope (mark only with evidence)
- [x] Choose and justify indexes from recorded query patterns (test candidates: `(category_id, status, created_at, id)` and `title`); note existing FK/PK indexes.
- [x] Add indexes via a **new migration**, not by rewriting initial schema migration.
- [x] Run `SHOW INDEX`, real `EXPLAIN` and optional `EXPLAIN ANALYZE` for **exact same** queries and values used in baseline.
- [x] Repeat equal number of DB-only timing runs under documented comparable cache/load conditions; show raw values plus summary and percent change only if justified.
- [x] Explicitly report no improvement/regressions and possible causes (selectivity, optimizer, filesort, LIMIT, COUNT, cache).
- [x] Document index write/storage trade-offs and 1M-row constraints (deep OFFSET, COUNT, keyset/cursor, dedicated search engine if needs change).

## Out of scope
- Benchmark fabrication, replacing MySQL with external search service.

## Acceptance criteria
- [x] Before/after evidence reproducible on identical rows and predicates.
- [x] Documentation explains index choices, non-improvements, and scaling caveats.

## Verification candidates (run only if applicable; never fake output)
- `php artisan migrate`
- `SHOW INDEX FROM designs;`
- `EXPLAIN SELECT ...; (same baseline SQL)`

## Suggested commit
`perf: index design search and compare plans`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (fill with real evidence)
- Commands and results: targeted `php artisan migrate --path=database/migrations/2026_10_29_160000_add_design_performance_indexes.php` passed; `php scripts/performance-baseline.php` before/after preserved count 100000 and checksum 3289254657. Ten timings/query, SQL/bindings, SHOW INDEX and EXPLAIN recorded in `docs/performance.md`. Post-index medians: category/status 0.9214 ms, prefix 2.3891 ms. `php artisan test`: 36 passed / 209 assertions (2.29s); Pint and diff check passed.
- Files changed: new performance-index migration, `docs/performance.md`, `docs/PLAN.md`, this spec, Phase 08 commit confirmation.
- Known issues / assumptions: warm local runs include PDO fetch overhead and uncontrolled background load; no production latency guarantee. Prefix still uses filesort. MariaDB automatically replaced standalone FK index with the supporting composite; rollback recreates the standalone index before dropping composite. EXPLAIN ANALYZE NOT RUN. Status: Awaiting Commit.
- Follow-up verification: `php scripts/verify-index-rollback.php` passed on separate local DB `dorra_index_verify_20261029_115223`. Forward/rollback/reapply all exit 0; expected indexes verified, fixture count 1 and checksum 3758889467 unchanged, FK category deletion rejected with error 1451 after rollback. Browser smoke NOT RUN: local browser access disabled in Work settings. Phase 10 remains gated on user confirmation of Phase 09 commit.
- Suggested commit message: `perf: index design search and compare plans`
- User commit confirmation / hash: user confirmed; verified `59d770f`.
