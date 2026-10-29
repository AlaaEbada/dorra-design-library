# Phase 04 — Sample Data

## Goal
Create a reproducible local 100k-design dataset without memory-heavy seeding.

## In scope (mark only with evidence)
- [ ] Seed 10+ categories, two demo users, exactly 100,000 designs, draft/published mix, English and Arabic titles and mixed created_at dates.
- [ ] Intentionally repeat some created_at values for tie-breaker tests; reuse ONE local placeholder thumbnail path for every design.
- [ ] Insert in bounded chunks (~1,000) with controlled memory; avoid 100k model `create()` calls.
- [ ] Make seeding and resetting idempotent or document a safe local-only reset sequence; report actual seeding duration, verify record counts and title samples.

## Out of scope
- Per-record image generation/downloads, production/real data.

## Acceptance criteria
- [ ] Fresh local seeded DB contains exactly 100,000 designs and 10+ categories.
- [ ] Both accounts exist; both statuses and both title languages exist.
- [ ] Document reproducible command and actual time.

## Verification candidates (run only if applicable; never fake output)
- `php artisan db:seed --class=DesignLibrarySeeder`
- `SELECT COUNT(*) FROM designs;`
- `SELECT status, COUNT(*) FROM designs GROUP BY status;`

## Suggested commit
`feat: seed design library sample dataset`

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
