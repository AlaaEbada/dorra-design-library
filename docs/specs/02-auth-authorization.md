# Phase 02 — Authentication & Authorization

## Goal
Implement internal login and server-side Admin/Viewer access control.

## In scope (mark only with evidence)
- [ ] Install/configure Breeze **Blade** if compatible; keep only login/logout (disable public registration, password reset and unused user management).
- [ ] Add `users.role` with `admin` / `viewer`, migration and appropriate validation/default; define policy and register as needed.
- [ ] Create deterministic local admin/viewer sample account seeding, with documented development-only credentials.
- [ ] Protect all library routes with `auth`; enforce policy on read/write routes and form requests; Viewer write requests must return 403.
- [ ] Conditionally hide Admin action links/buttons in Blade; both roles may view draft/published.

## Out of scope
- Spatie Permission, external identity providers, public sign-up.

## Acceptance criteria
- [ ] Unauthenticated library request redirects to login.
- [ ] Admin access works, Viewer direct write URLs/requests denied by server.
- [ ] Public registration and password recovery are unavailable.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=Authorization`
- `git diff --check`

## Suggested commit
`feat: add internal auth and design roles`

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
