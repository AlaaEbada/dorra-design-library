# Phase 06 — Design CRUD

## Goal
Implement Admin create/show/edit/update/delete safely and predictably.

## In scope (mark only with evidence)
- [ ] Create controllers, resource routes, Blade forms/pages and Form Requests for store/update.
- [ ] Validate title (required, max length), existing category ID, status draft/published; thumbnail is the shared placeholder, not an upload.
- [ ] Show per-field errors and old input, success flash messages, show and empty states.
- [ ] Authorize all write actions in policy (and Form Requests where applicable), return 403 for Viewer including direct POST/PATCH/DELETE; allow both roles to show drafts.
- [ ] Apply CSRF to forms; method spoofing for PUT/PATCH/DELETE; use route-model binding.

## Out of scope
- Soft delete, audit log, file uploads, category/admin management.

## Acceptance criteria
- [ ] Admin can create/update/delete, persistent changes visible.
- [ ] Invalid inputs are rejected without writes; Viewer direct writes denied.
- [ ] Blade hides unavailable actions and requests remain server-protected.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=DesignCrud`
- `git diff --check`

## Suggested commit
`feat: implement policy-protected design CRUD`

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
