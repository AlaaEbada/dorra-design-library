# Phase 05 — Listing & Search

## Goal
Implement fast server-side filtering and ordered 24-per-page listing.

## In scope (mark only with evidence)
- [x] Before code, explain Prefix vs Word vs Substring briefly and **ask user to confirm Prefix**; record decision.
- [x] Use validated query params for title/category/status, combined AND behavior, prepared bindings and correct LIKE wildcard escaping.
- [x] Show title, category, status, placeholder thumbnail and created_at; eager-load category.
- [x] Sort `created_at DESC, id DESC`; paginate 24 with `withQueryString()`; clear filters by linking to clean index URL.
- [x] Display explicit search hint (starts with title); confirm Arabic/English collation behavior and document examples/limitations.
- [x] Show empty result state and field/filter errors appropriately.

## Out of scope
- FULLTEXT / dedicated search engine; client-side retrieval of 100k records.
- Writing designs (Phase 06).

## Acceptance criteria
- [x] Filters work separately and together; reset removes query params.
- [x] Newest-first tie-breaker is stable; pagination preserves filters.
- [x] Actual SQL only queries DB pages and does not load all designs.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=DesignListing`
- `git diff --check`

## Suggested commit
`feat: add design listing and prefix search`

## Execution checklist
- [x] Inspect current code/repo state and read this spec before edits.
- [x] Implement only the items above.
- [x] Run and record relevant real verification; leave unverified requirements unchecked.
- [x] Update this spec and `docs/PLAN.md` with facts and evidence.
- [x] Stop at `Awaiting Commit`; send changed files, results, issues and a suggested commit message.

## Completion record (2026-10-29)
- Status: Done; user approved continuation after verified commit 4901b19.
- Decision: user explicitly approved Prefix after explanation of Prefix vs Word vs Substring, and requested library/search UI improvements. Prior phase commit 8bd0025 verified; continuation approved and Phase 04 recorded Done.
- Created: app/Http/Controllers/DesignListingController.php (thin invokable read controller); app/Http/Requests/DesignListingRequest.php (policy + query validation, safe redirect to clean index); public/css/library.css (responsive local styling); tests/Feature/DesignListingTest.php (small isolated datasets).
- Modified: app/Models/Design.php (DB-side filter scope with bindings); routes/web.php (same protected library URL, now controller); resources/views/library.blade.php (filter form/cards/count/empty state/previous-next pagination); resources/views/layouts/base.blade.php (shared CSS link, responsive shell/skip link); README.md; docs/PLAN.md; this spec; Phase 04 spec (commit/approval closure). No CRUD, new indexes, dependencies or external assets.
- UI: ivory/green editorial layout, role/account and logout, cards with thumbnail/category/status/date/title, clear filters and starts-with hint, localized title direction via dir=auto. 4/3/2/1-column breakpoints; same shared CSS improves login shell. No fabricated create/detail links before CRUD phase.
- Validation: nullable title string max255; existing integer category_id; enum status; positive integer page. Invalid browser GET redirects to clean /library with field errors to avoid a back-redirect loop; no unsafe raw filter values are rendered. Unknown query params do not enter SQL conditions. Auth + registered policy stay on route and Form Request.
- Commands/results:
  - Preflight Get-Location, git status --short --branch (clean), git log -1 --oneline (8bd0025); instructions/plan/spec read before changes.
  - `php artisan view:cache`: exit 0, compiled Blade templates. Later browser/test requests compiled updated templates normally.
  - Initial `php artisan test --filter=DesignListing`: FAILED before collection due to incorrectly escaped backslash string in test. Corrected test literal. Next run FAILED, 7 requests returned 403 because newly factory-created test user had not reloaded its database-default role. Corrected test setup to fresh() user, matching real persisted login behavior; no auth relaxation.
  - Rerun `php artisan test --filter=DesignListing`: exit 0, 7 passed / 51 assertions. Covers English/Arabic prefix, literal percent/underscore/!/backslash/apostrophe, standalone + combined AND filters, stable ties and second page, query preservation, invalid fields, no-match/reset, HTML escaping and eager-query count/LIMIT.
  - PHP bootstrap probe on project MariaDB: server 10.4.32-MariaDB, title utf8mb4_unicode_ci from SHOW FULL COLUMNS. Authenticated Viewer internal HTTP /library?title=Design&status=published returned 200 with exactly 24 card elements. Captured DB statements below.
  - Separate existing verification DB dorra_schema_verify_da65ed11: transactionally inserted small literal-symbol fixtures, tested Design::filter() under original sql_mode and with NO_BACKSLASH_ESCAPES; each of 100%, a_, a!, backslash prefix, O'Reilly matched exactly one row in both modes. Rolled back fixture rows and restored session mode. Final design count 100000; main dataset untouched.
  - Direct bound collation probes using CAST(? AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci LIKE ? ESCAPE '!': Design/design=1, Café/Cafe=1, أمل/امل=0, تَصميم/تصميم=0. Documented limitations, no Arabic normalization claims.
  - `php artisan serve --host=127.0.0.1 --port=8015`: local server started. Browser login as Admin successful; page displayed 100000 results/24 cards. Arabic prefix تصميم + Branding + published returned 2084 results; Next preserved all filters and page=2; Clear returned clean /library and 100000. No-match query showed empty state; sign out returned to login. Viewer login and library also successful.
  - Browser screenshots inspected at desktop 1365x900 and mobile 390x844; layout adjusts to four/two columns. DOM measurements: desktop innerWidth1366/scrollWidth1350; mobile innerWidth390/scrollWidth375; no horizontal overflow. Screenshot saved as external preview artifact, not committed project file. Temporary viewport reset. Not an exhaustive accessibility or cross-browser audit.
  - `vendor/bin/pint --dirty`: exit 0, active-phase PHP formatting; `vendor/bin/pint --dirty --test`: exit 0, passed.
  - `php artisan test`: exit 0, 29 passed / 144 assertions; includes previous auth/schema/seeding tests, no 100k rows seeded in tests.
  - `php artisan route:list --except-vendor`: exit 0, same five application routes; library handled by controller, login/logout unchanged.
  - `git diff --check`: exit 0, only line-ending normalization warnings.

### Actual captured SQL and bindings (live 100k project DB)

```sql
select `id`, `name` from `categories` order by `name` asc;
-- []
select count(*) as aggregate from `designs`
 where title LIKE ? ESCAPE '!' and `status` = ?;
-- ["Design%", "published"]
select * from `designs` where title LIKE ? ESCAPE '!' and `status` = ?
 order by `created_at` desc, `id` desc limit 24 offset 0;
-- ["Design%", "published"]
select `id`, `name` from `categories` where `categories`.`id` in
 (13,14,15,16,17,18,19,20,21,22,23,24);
-- [] (integer eager-load IDs inserted by Eloquent)
```

- Escaping: ! -> !!, % -> !%, _ -> !_; backslash remains literal with explicit ESCAPE '!'. Wildcard suffix appended after escaping. Uses bindings, no raw user interpolation. No contains/word search. Laravel trims surrounding query whitespace.
- Limitations: MySQL server itself NOT RUN; no performance benchmark/EXPLAIN claimed in this phase, no extra index. Arabic hamza/diacritic variants may differ; SQLite collation is not MariaDB collation. Actual CRUD/Form Request write enforcement remains Phase 06.
- Suggested commit: `feat: add design listing and prefix search`
- User approved continuation after commit; git log verified 4901b19. No staging, commit or push by agent.
