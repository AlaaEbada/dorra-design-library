# Dorra — Execution Plan

## How to use
Read `../AGENTS.md`, then the next phase spec. The Agent updates checkboxes **only on verification**. Phase status is `Pending` → `In Progress` → `Awaiting Commit` → `Done` (or `Blocked`). `Done` requires user confirmation of commit, not merely completed code. Always pause at `Awaiting Commit`.

## Phase tracker

| # | Module | Depends on | Status | Evidence / commit |
|---|---|---|---|---|
| 01 | [Environment & Setup](specs/01-environment-setup.md) | — | Done | User confirmed commit; verified c28ce8482edd8a69101f7fa1b0dbab7a553b3add. Working tree clean before Phase 02. |
| 02 | [Authentication & Authorization](specs/02-auth-authorization.md) | 01 | Pending | — |
| 03 | [Schema & Models](specs/03-schema-models.md) | 02 | Pending | — |
| 04 | [Sample Data](specs/04-sample-data.md) | 03 | Pending | — |
| 05 | [Listing & Search](specs/05-listing-search.md) | 04 | Pending | — |
| 06 | [Design CRUD](specs/06-design-crud.md) | 05 | Pending | — |
| 07 | [Feature Tests](specs/07-feature-tests.md) | 06 | Pending | — |
| 08 | [Performance Baseline](specs/08-performance-baseline.md) | 07 | Pending | — |
| 09 | [Indexes & Performance Comparison](specs/09-indexes-comparison.md) | 08 | Pending | — |
| 10 | [README & Final Verification](specs/10-readme-handoff.md) | 09 | Pending | — |

## Critical dependency constraints
- Keep extra performance indexes **out of Phase 03** so Phase 08 measures baseline on the identical 100,000-record dataset before Phase 09.
- Confirm prefix search before implementing Phase 05; document limitations and UI hint.
- Phase 07 uses small isolated test data and must not run Phase 04 bulk seeding inside tests.
- Each phase ends with a report and pause for the user’s manual commit. Never automatically start the next phase.

## Work log (append factual entries only)

| Date | Phase | Change / proof | Blocker | Commit (verified) |
|---|---|---|---|---|
| 2026-10-29 | 01 | PHP 8.2.12, Laravel 12.70.2, Composer 2.7.9, Node 24.13.0, npm 11.7.0; platform check passed; MariaDB 10.4.32; initial migrations Ran; internal GET / 200; CSRF enabled | No Git repository; sample env still SQLite; existing app retained per user request | None verified |

## Phase 01 follow-up (2026-10-29)

User authorized Git initialization and sample env update. `git init` succeeded;
`.env.example` uses mysql placeholders; README documents local setup and MariaDB.
Git ignore checks, sample-env parsing and heuristic candidate secret scan passed.
No tracked files or commits; internal GET / 200, CSRF enabled, migrations remain Ran.
Changed: .env.example, README.md, docs/PLAN.md, docs/specs/01-environment-setup.md;
.git initialized. Phase 01 awaits manual review/commit; Phase 02 not started.
Browser, frontend build and separate fresh setup NOT RUN.

## Final release gates
- [ ] Phase 01–10 acceptance criteria independently verified.
- [ ] 100,000-row local sample dataset reproducible; admin and viewer accounts confirmed.
- [ ] SQL, EXPLAIN and repeated before/after index measurements documented with real outputs.
- [ ] Actual feature-test command and results recorded.
- [ ] README and `.env.example` reviewed; credentials are sample-only; no secrets.
- [ ] User confirms final commit/repository delivery.

Phase 01 commit confirmed by user and verified with git log/show: c28ce8482edd8a69101f7fa1b0dbab7a553b3add. User authorized Phase 02.

| 2026-10-29 | 02 | Additive role migration and sample account seeder succeeded on local MariaDB; 11 tests / 60 assertions; Pint and diff check passed | Browser not run; real CRUD enforcement deferred to Phase 06 | Awaiting user commit |
