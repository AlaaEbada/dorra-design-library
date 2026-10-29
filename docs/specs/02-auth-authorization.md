# Phase 02 — Authentication & Authorization

## Goal
Implement internal login and server-side Admin/Viewer access control.

## In scope (mark only with evidence)
- [x] Install/configure Breeze **Blade** if compatible; keep only login/logout (disable public registration, password reset and unused user management).
- [x] Add `users.role` with `admin` / `viewer`, migration and appropriate validation/default; define policy and register as needed.
- [x] Create deterministic local admin/viewer sample account seeding, with documented development-only credentials.
- [x] Protect all library routes with `auth`; enforce policy on read/write routes and form requests; Viewer write requests must return 403.
- [x] Conditionally hide Admin action links/buttons in Blade; both roles may view draft/published.

## Out of scope
- Spatie Permission, external identity providers, public sign-up.

## Acceptance criteria
- [x] Unauthenticated library request redirects to login.
- [x] Admin access works, Viewer direct write URLs/requests denied by server.
- [x] Public registration and password recovery are unavailable.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=Authorization`
- `git diff --check`

## Suggested commit
`feat: add internal auth and design roles`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (2026-10-29)
- Status: Done. User confirmed review/commit and authorized Phase 03.
- Scope: internal login/logout only; configured Breeze Blade selectively from installed stubs, not the full installer. Simple server-rendered layout has no frontend build dependency. No unused auth controllers, routes or screens were generated.
- Files created: app/Enums/Role.php; app/Http/Controllers/Auth/AuthenticatedSessionController.php; app/Http/Requests/Auth/LoginRequest.php; app/Policies/LibraryPolicy.php; database/migrations/2026_10_29_000000_add_role_to_users_table.php; database/seeders/SampleAccountSeeder.php; resources/views/layouts/base.blade.php; resources/views/auth/login.blade.php; resources/views/library.blade.php; tests/Feature/AuthorizationTest.php.
- Files modified: app/Models/User.php (enum cast, role excluded from mass assignment); app/Providers/AppServiceProvider.php (policy registration); routes/web.php; database/seeders/DatabaseSeeder.php; tests/Feature/ExampleTest.php; composer.json/lock; README.md; docs/PLAN.md; this spec. Phase 01 spec also records user-confirmed verified commit c28ce8482edd8a69101f7fa1b0dbab7a553b3add.
- Commands/results:
  - `composer show laravel/breeze --all`: exit 0; published requirements support PHP 8.2 and Laravel 12.
  - `composer require laravel/breeze:^2.4 --dev --no-interaction`: exit 0; PowerShell passed constraint as 2.4 (Composer warned it was strict); locked and installed v2.4.0, no other package upgrades; no vulnerability advisories reported.
  - `php artisan test --filter=Authorization`: initial FAILED, 2 failures / 7 passed: middleware interpreted short resource name as a route parameter. Corrected to registered fully qualified policy resource; rerun exit 0, 9 passed / 57 assertions.
  - `php artisan migrate --no-interaction`: exit 0; additive users.role migration applied on existing local MariaDB. No reset or destructive commands.
  - `php artisan db:seed --class=SampleAccountSeeder --no-interaction`: exit 0. Local sample admin/viewer accounts created; seeder repeatability verified in isolated test database.
  - `php artisan route:list --except-vendor`: exit 0, only root redirect, library GET, login GET/POST, logout POST. Registration/recovery/profile routes absent.
  - `vendor/bin/pint --dirty`: exit 0, formatted active-phase PHP files; subsequent `vendor/bin/pint --dirty --test`: exit 0, passed.
  - `php artisan test`: exit 0; 11 passed, 60 assertions. Tests use RefreshDatabase and SQLite :memory:, no bulk sample seeding. Covers login/logout, validation, failed-login throttling, guest redirect, policy/forged writes, conditional admin content, disabled auth routes, role default/mass assignment, repeatable accounts and CSRF 419 outside test bypass.
  - PHP stdin bootstrap probe: exit 0; server 10.4.32-MariaDB, both local accounts have expected role and Hash::check succeeds; internal GET /login 200.
  - `git diff --check`: exit 0; line-ending normalization warnings only.
- Current library route is protected by auth + policy; LoginRequest validates authentication. Both roles have unconditional read access (no status restriction).
- Scope boundary: design records/status UI and CRUD do not exist until later phases. Actual CRUD routes, write Form Requests and Admin action links cannot be verified yet; policy write abilities are verified through test-only endpoints (not exposed by application). Admin-only shell content is verified. Connect policy to every future write endpoint/Form Request in Phase 06 and retest with real records.
- Limitations: browser/network interaction and frontend build NOT RUN; MySQL server behavior NOT VERIFIED (local MariaDB). No production credentials introduced; README sample credentials are local-only. No commit performed by agent.
- Suggested commit message: `feat: add internal auth and design roles`
- User commit confirmation: received; Commit `8ae4c4c` (`feat: add internal auth and design roles`) verified with `git log -1 --oneline`; working tree was clean before this documentation correction.
- Quick recheck: `php artisan test` exit 0, 11 passed / 60 assertions; `vendor/bin/pint --dirty --test` passed; `git diff --cached --check` exit 0; route list shows expected five routes; migrate status shows all four migrations Ran. Corrected Phase 02 tracker from Pending to Awaiting Commit and moved its work-log row into the table. No application changes.
