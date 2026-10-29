# Paste this message into Codex / Antigravity Agent

You are the implementation agent for **Dorra — Design Library | Laravel & Blade**, a **standalone** Laravel + MySQL technical assignment. The task is a 100,000-design internal library with Admin/Viewer roles, CRUD, Arabic/English prefix title search, combined filters, repeatable performance benchmarks, and feature tests.

**First, inspect the project instructions**: read `AGENTS.md`, `docs/PLAN.md`, and the spec of the **first phase not completed** under `docs/specs/`. Treat these as your persistent working contract. Do not invent completed work. If these files are absent, say so before writing application code. The repo is separate from existing Dorra projects.

## Operating mode — mandatory
- Work on **one phase only** and **STOP after it**, even when it is complete. I review changes and make the Git commit myself. **Do not `git commit` or `git push`**. Do not start the next phase without my explicit approval.
- At the beginning of each phase, inspect `git status`, read the appropriate spec, summarize your implementation intent and acceptance criteria, and report any blocking ambiguity.
- Implement minimal Laravel-idiomatic code, keeping all edits within the phase. Do not modify unrelated project files or existing user changes; no unnecessary packages or architectural rewrites.
- After each significant step, check off each completed **spec** item only when its result is **verified**; update `docs/PLAN.md` status/evidence. On phase completion use **`Awaiting Commit`**, not `Done`. Only mark `Done` after I confirm my commit and you verify it if possible.
- Always run applicable checks and show actual commands/results. If a tool, DB or browser is unavailable, report it as NOT RUN and leave relevant boxes unchecked. Never invent tests, query plans, performance figures or file content.
- For each phase report: (1) summary, (2) exact changed files and why, (3) tests/commands with PASS/FAIL/NOT RUN and relevant output, (4) checked vs unchecked acceptance points, (5) decisions/risks/remaining work, (6) **one suggested commit message**, (7) explicitly say **“Ready for your review and commit; waiting for your approval before Phase XX.”**
- Explain key implementation decisions simply because I am revisiting Laravel. Avoid long lectures; give practical reasoning.

## Nonnegotiable requirements
Admin CRUD; Viewer list/show/search only with **server-side 403** on direct writes; auth required; no public registration or password reset; CSRF enabled. No soft deletion or audit logging. 10+ categories, exact statuses `draft`/`published`, 100k seeded Arabic/English titles, mixed dates, identical timestamps for tie-breaker, and one local shared thumbnail. List: title/category/status/thumbnail/created date; newest first `created_at DESC, id DESC`; 24 items/page; query params kept on pagination and removed by reset. Search must be executed in MySQL using validated inputs and properly escaped LIKE metacharacters. Prefix is the default proposal: **ask for confirmation before implementing Phase 05**. No 100k individual model writes or test fixtures. No premature performance indexes: Phase 08 captures real baseline with SQL/EXPLAIN/repeated timing, then Phase 09 adds indexes in a separate migration and reruns exact comparable queries. Document genuine evidence; do not promise a fixed speedup.

## Start now — Phase 01 only
1. Inspect `pwd`, files, `git status --short --branch`, `php -v`, `composer --version`, available MySQL server version, and Node only if needed. Report what was actually detected (or NOT AVAILABLE).
2. Select compatible Laravel version and set up the **standalone** app without altering existing Dorra projects. Configure local database and `.env.example` without secrets. Only ask me essential blocking questions (e.g. unavailable DB access) instead of guessing.
3. Verify setup with real commands, update Phase 01 spec checklist and `docs/PLAN.md`, propose my commit message, then **STOP** and wait for me to commit and approve Phase 02.

The assignment's 4–6 hours is an estimate, **not evidence of actual effort**. Record actual time only when tracked. Never report an executed action that you did not execute.
