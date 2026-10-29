# Phase 10 — README & Final Verification

## Goal
Deliver a reproducible, auditable standalone repository.

## In scope (mark only with evidence)
- [ ] Write README with PHP/Laravel/MySQL/Composer/Node **observed** versions; install/config/run commands and safe `.env.example`.
- [ ] Document sample accounts, roles, setup/seed/reset commands **with destructive local reset warning**, testing command and actual results.
- [ ] Explain selected search mode and exact Arabic and English prefix examples, wildcard escaping/collation/diacritics/word boundaries limitations and when search service makes sense.
- [ ] Link `docs/performance.md` with actual SQL, plans, pre/post measurements and 1M scaling notes.
- [ ] Record **actual** elapsed effort, assumptions and unresolved tasks (do not substitute 4–6h estimate).
- [ ] Review all acceptance tasks/phase statuses, repository diff, no secrets; show any skipped checks and final handoff.

## Out of scope
- Additional product features, fake screenshot/benchmark, automatic git commits.

## Acceptance criteria
- [ ] Fresh setup instructions reproducibly generate dataset and demo accounts.
- [ ] Feature test and performance proof are linked, accurate and complete.
- [ ] Agent stops for final user commit and does not make unsupported delivery claims.

## Verification candidates (run only if applicable; never fake output)
- `php artisan test`
- `git diff --check`
- `git status --short --branch`

## Suggested commit
`docs: finalize design library handoff`

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
