# AGENTS.md — Dorra Design Library

## Mission and source of truth
Build **Dorra — Design Library**, a standalone Laravel + Blade + MySQL assignment with sample data only. Read, in order, `AGENTS.md`, `docs/PLAN.md`, and the active `docs/specs/NN-*.md` **before any change**, including after context resets. The task brief and phase specs define scope; if inconsistent, report the conflict to the user before implementing. `docs/PLAN.md` is the execution ledger, not proof on its own.

## Workflow: one module at a time
1. Inspect repo state (`pwd`, `git status --short --branch`) and available tools; check `docs/PLAN.md` for the **first Pending or In Progress** phase. If a preceding phase has `Awaiting Commit` approval, **do not start the next phase**.
2. Read the matching spec, state scope and acceptance criteria, and implement **only that phase** with the minimum changes necessary. Keep changes small and Laravel-idiomatic.
3. Run relevant verification (tests, lint, migrations, manual requests/SQL as applicable); capture real commands, outcomes and failures. Never fabricate results, paths, timings, logs, query plans or environment versions.
4. Update each task checkbox in the phase spec only after verification. Mark unmet/untested items `[ ]` and explain blockers. Update the phase row in `docs/PLAN.md` with status and evidence.
5. When all acceptance criteria are verified, mark the phase `Awaiting Commit` (not `Done`), list changed files, commands, results, risks, and propose a clear Git commit message. **STOP** and ask the user to review and commit.
6. Only after the user confirms they have committed may you mark that phase `Done` (record the commit hash if provided/verified) and, with explicit authorization, begin the next phase. Do not claim a commit exists without checking it.

**Statuses:** `Pending`, `In Progress`, `Blocked`, `Awaiting Commit`, `Done`. `Done` requires acceptance evidence **and** user confirmation of the commit. Never mark a whole phase done based only on code written. Keep unchecked tasks as unchecked.

## Git and safety
- **Never run `git commit`, `git push`, `git reset --hard`, `git clean -fd`, force-push, rewrite history, or delete unrelated files** unless the user explicitly authorizes that particular action. User makes commits by default.
- Do not change, format, rename, or delete files outside active-phase scope unless technically required; explain any unavoidable cross-cutting change first.
- Never overwrite uncommitted user work. Inspect diffs before modifying existing files. Preserve existing code unless changes are essential to the active spec.
- No destructive DB commands (`migrate:fresh`, `db:wipe`, `TRUNCATE`, etc.) without clearly identifying the target **sample/local database** and obtaining user approval. Never operate on production data.
- Never put passwords, private keys or tokens in git. `.env.example` may contain placeholders only; development credentials must be explicitly documented as **local sample credentials**.
- Respect existing PHP/Laravel compatibility after verifying installed versions. Don't assume versions or report unsupported compatibility as fact.

## Architecture and coding conventions
- Laravel conventions; controllers thin, validation via Form Requests, authorization via Policy (and middleware where useful), Eloquent relationships, eager loading and DB-side query conditions.
- No role/permission package, search engine, paid service, upload pipeline, user administration, public registration, password reset, soft delete or audit logs. Breeze Blade is allowed for login; disable public registration and unused auth screens/routes.
- Admin can CRUD; Viewer can list/show/search only. Enforce permissions **server-side** on every write route, including forged requests. Both roles can see draft/published. CSRF must remain enabled.
- Exactly `draft` and `published` statuses; 10+ seeded categories; title/foreign key/status/thumbnail/creation date. One local placeholder thumbnail reused by all designs.
- List ordering must be `created_at DESC, id DESC` and page size 24; preserve search/filter parameters. Query data in MySQL, never materialize 100k records into PHP or browser.
- Search defaults to **title prefix** (`term%`) but the user must confirm it at the listing phase. Escape LIKE wildcards (`%`, `_`, `\`) using an explicitly compatible MySQL ESCAPE convention and bindings; verify Arabic/English behavior and collation.
- For a valid performance comparison, **do not add custom performance indexes in the schema phase**. Baseline with 100k rows first, then create a separate index migration, remeasure under comparable conditions; preserve actual SQL, bindings, EXPLAIN, environment and repeated measurements.
- Test on a small isolated dataset (`RefreshDatabase`/factories); do not seed 100k in feature tests.

## Reporting and honesty
- Report actual commands and their exit status/relevant output; clearly label unrun checks as **NOT RUN**, failures as **FAILED**, assumptions as **ASSUMED**.
- Do not claim an endpoint, UI or benchmark works without direct evidence. Do not invent response times or `EXPLAIN` plans.
- After each phase, explain (briefly) what changed, why files exist, a key Laravel concept, the verification evidence, open issues, and the **suggested user commit message**.
- Work must stop after each module, even if there are more phases. Never silently continue.
- Keep `docs/PLAN.md` and the active spec accurate throughout the work, including partial progress or blockers.
