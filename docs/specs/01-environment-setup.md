# Phase 01 — Environment & Setup

## Goal
Verify the actual environment and bootstrap a separate repository with a compatible Laravel/Blade application.

## In scope (mark only with evidence)
- [x] Run and record `pwd`, repo contents, `git status --short --branch`, `php -v`, `composer --version`, MySQL server version, and Node/npm versions if needed.
- [x] Select a supported Laravel version after environment check; record PHP/Laravel/MySQL compatibility decision.
- [x] Create or configure a new standalone project/repo without touching other projects.
- [x] Configure local MySQL through `.env` (untracked) and safe `.env.example`; keep CSRF enabled.
- [x] Verify application boots; create project-local `AGENTS.md`/`docs/` structure if not already present.

## Out of scope
- Auth implementation, data schema, feature pages.

## Acceptance criteria
- [x] Versions documented from commands, not assumptions.
- [x] App boots using documented setup and no secrets are committed.

## Verification candidates (run only if applicable; never fake output)
- `php -v`
- `composer --version`
- `php artisan --version`
- `php artisan about`
- `git status --short --branch`

## Suggested commit
`chore: bootstrap Laravel design library`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (2026-10-29)
- Status: Awaiting Commit; application code unchanged. Phase 02 NOT STARTED.
- Scope clarification: the existing application is retained as explicitly requested. The original new-project wording above conflicts with that request; no new application was created. User subsequently authorized Git initialization and the sample env update.
- Commands and results (exit 0 unless stated):
  - `Get-Location`: `E:\Mohamed Elbaz\dorra-design-library`; `Get-ChildItem -Force` confirmed Laravel directories, vendor, .env, AGENTS.md and docs. All ten phase specs exist.
  - `git status --short --branch`: FAILED, exit 128, not a Git repository. No Git history or commit can be verified.
  - `php -v`: PHP 8.2.12; `composer --version`: Composer 2.7.9.
  - `node --version`: v24.13.0; `npm --version`: 11.7.0.
  - `php artisan --version`: Laravel Framework 12.70.2.
  - `composer check-platform-reqs`: all installed package platform requirements passed. Retain installed Laravel/PHP; this verifies installed requirements, not upstream support lifecycle.
  - `php artisan migrate:status`: all three initial migrations Ran, batch 1. No migrations were executed.
  - PHP stdin bootstrap probe (`require vendor/autoload.php`, console Kernel bootstrap): configured driver mysql; `DB::selectOne('SELECT VERSION() AS version')` returned 10.4.32-MariaDB. MariaDB differs from assignment MySQL; MySQL-specific performance behavior NOT VERIFIED.
  - Same probe: HTTP Kernel handling `Request::create('/', 'GET')` returned 200; web middleware includes ValidateCsrfToken. This is an in-process request; browser/network smoke check NOT RUN.
- Files inspected: composer.json, routes/web.php, config/auth.php, app/Models/User.php, initial migrations, bootstrap/app.php, package.json, .env.example, .gitignore. Default welcome route and User model remain; authentication implementation is out of scope.
- Files changed: .env.example, README.md, docs/PLAN.md and this spec; .git initialized.
- Follow-up authorized by user: `git init` succeeded (exit 0); no branch switch, staging, commit or push performed. `git status --short --branch` now succeeds and reports no commits yet on master; existing project files are untracked.
- `.env.example` now uses mysql with local sample placeholders; actual .env remains unchanged. Dotenv parser probe passed: mysql driver, expected username/password placeholders and empty APP_KEY.
- README now documents existing-environment startup, verified versions, MariaDB difference and separate fresh-setup instructions. Fresh setup against a second database, frontend build and browser checks NOT RUN.
- Follow-up verification (all exit 0): `git check-ignore -v .env vendor/autoload.php` confirms exclusion; `git ls-files` is empty (no tracked files); `git ls-files --others --exclude-standard` reviewed for candidate paths; heuristic secret-signature scan returned no matches (not a guarantee); `php artisan migrate:status` still shows three Ran migrations. Repeated internal GET / returned 200; DB version and CSRF checks unchanged.
- Remaining limitation: no commit exists. Nothing has been committed; user must review all initial project files before staging. Phase 02 remains pending.
- Acceptance: verified for this existing local environment; awaiting user review and manual commit. Do not mark Done without user confirmation and verification.
- Suggested commit message after resolution: `chore: verify Laravel environment and document setup`
- User commit confirmation / hash: pending; no commit verified.
