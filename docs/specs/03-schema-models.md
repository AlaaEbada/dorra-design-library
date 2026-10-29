# Phase 03 — Schema & Models

## Goal
Create minimal schema and Eloquent relations without premature performance indexes.

## In scope (mark only with evidence)
- [ ] Create `categories` and `designs` migrations; `designs` includes ID, title, category_id FK, status draft/published, thumbnail path, timestamps (created_at required).
- [ ] Create `Category` and `Design` models, proper fillable/casts, `Design belongsTo Category` and `Category hasMany Designs`.
- [ ] Create factories for small test fixtures and sample-data command reuse.
- [ ] Ensure status constraints/allowed values are enforced in validation (and DB constraint if database compatibility permits).
- [ ] Inspect existing indexes with `SHOW INDEX FROM designs`; document PK/FK indexes MySQL creates automatically.

## Out of scope
- Extra title/composite performance indexes (Phase 09 only).
- Soft deletes, audit logs, uploads, category CRUD.

## Acceptance criteria
- [ ] Migrations run on a fresh local DB.
- [ ] Foreign key and relationships behave correctly.
- [ ] No deliberate extra performance indexes exist before baseline.

## Verification candidates (run only if applicable; never fake output)
- `php artisan migrate:status`
- `php artisan migrate`
- `SHOW INDEX FROM designs;`

## Suggested commit
`feat: add design and category schema`

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
