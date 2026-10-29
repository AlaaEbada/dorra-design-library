# Phase 06 — Design CRUD

## Goal
Implement Admin create/show/edit/update/delete safely and predictably.

## In scope (mark only with evidence)
- [x] Create controllers, resource routes, Blade forms/pages and Form Requests for store/update.
- [x] Validate title (required, max length), existing category ID, status draft/published; thumbnail is the shared placeholder, not an upload.
- [x] Show per-field errors and old input, success flash messages, show and empty states.
- [x] Authorize all write actions in policy (and Form Requests where applicable), return 403 for Viewer including direct POST/PATCH/DELETE; allow both roles to show drafts.
- [x] Apply CSRF to forms; method spoofing for PUT/PATCH/DELETE; use route-model binding.

## Out of scope
- Soft delete, audit log, file uploads, category/admin management.

## Acceptance criteria
- [x] Admin can create/update/delete, persistent changes visible.
- [x] Invalid inputs are rejected without writes; Viewer direct writes denied.
- [x] Blade hides unavailable actions and requests remain server-protected.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=DesignCrud`
- `git diff --check`

## Suggested commit
`feat: implement policy-protected design CRUD`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (fill with real evidence)
- Commands and results:
  - `vendor\bin\phpunit --filter=DesignCrud` — passed: 7 tests, 65 assertions.
  - `vendor\bin\phpunit` — passed: 36 tests, 209 assertions.
  - `php artisan route:list --path=designs` — passed: seven expected routes, including the Admin dashboard and six CRUD routes.
  - `vendor\bin\pint --test` — passed.
  - `git diff --check` — passed.
- Files changed: `app/Http/Controllers/DesignController.php`, `app/Http/Requests/StoreDesignRequest.php`, `app/Http/Requests/UpdateDesignRequest.php`, `app/Policies/DesignPolicy.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`, the Admin dashboard and CRUD Blade views/partials, library CSS/view, `tests/Feature/DesignCrudTest.php`, and phase documentation. The dashboard shows a fixed shared thumbnail beside every design, and status badges are scoped to library cards.
- Known issues / assumptions: at user request, Admin management is consolidated in the separate `/admin/designs` dashboard; the library and shared details page remain read-only. Browser smoke test was NOT RUN because local browser control is disabled in the Work settings; feature tests use an isolated temporary database and do not modify the local 100,000-row sample dataset.
- Suggested commit message: `feat: implement policy-protected design CRUD`
- User commit confirmation / hash: pending
