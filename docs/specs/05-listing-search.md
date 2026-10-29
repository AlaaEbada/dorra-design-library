# Phase 05 — Listing & Search

## Goal
Implement fast server-side filtering and ordered 24-per-page listing.

## In scope (mark only with evidence)
- [ ] Before code, explain Prefix vs Word vs Substring briefly and **ask user to confirm Prefix**; record decision.
- [ ] Use validated query params for title/category/status, combined AND behavior, prepared bindings and correct LIKE wildcard escaping.
- [ ] Show title, category, status, placeholder thumbnail and created_at; eager-load category.
- [ ] Sort `created_at DESC, id DESC`; paginate 24 with `withQueryString()`; clear filters by linking to clean index URL.
- [ ] Display explicit search hint (starts with title); confirm Arabic/English collation behavior and document examples/limitations.
- [ ] Show empty result state and field/filter errors appropriately.

## Out of scope
- FULLTEXT / dedicated search engine; client-side retrieval of 100k records.
- Writing designs (Phase 06).

## Acceptance criteria
- [ ] Filters work separately and together; reset removes query params.
- [ ] Newest-first tie-breaker is stable; pagination preserves filters.
- [ ] Actual SQL only queries DB pages and does not load all designs.

## Verification candidates (run only if applicable; never fake output)
- `php artisan route:list`
- `php artisan test --filter=DesignListing`
- `git diff --check`

## Suggested commit
`feat: add design listing and prefix search`

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
