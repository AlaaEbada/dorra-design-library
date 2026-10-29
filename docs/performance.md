# Design query performance

## Baseline — 2026-10-29 (before custom indexes)

Application revision: `fc3bbe0`. Command: `php scripts/performance-baseline.php`.
The script builds queries using the application's `Design::filter()` scope and
the same `created_at DESC, id DESC`, page size 24, and page-one offset as the library.
It executes inside a read-only transaction and rolls back on exit.

### Environment and dataset

- Local database: `dorra_design_library`, exact `COUNT(*)`: **100000**.
- Windows 11 AMD64, build 26300; Intel Core i7-12700H, 14 cores / 20 logical processors.
- Physical RAM: 16869548032 bytes (approximately 15.71 GiB).
- PHP CLI 8.2.12; Laravel 12.70.2; MariaDB 10.4.32.
- InnoDB, Dynamic row format, table/title/connection collation `utf8mb4_unicode_ci`.
- InnoDB buffer pool: 16777216 bytes (16 MiB).
- Query cache: OFF; configured size 1048576 bytes.
- SQL mode: `ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION`.
- Table data length: 11026432 bytes; index length: 3686400 bytes.
- `CHECKSUM TABLE designs`: **3289254657**. Preserve this dataset for Phase 09.
- Table metadata estimated 99510 rows; this is an estimate, while `COUNT(*)` returned 100000.

The user deleted one sample design during manual CRUD testing. With explicit
approval, a replacement was inserted before measurement: id 133001,
title `Recovered local sample`, category 16 (Photography), status `draft`,
thumbnail `images/design-placeholder.svg`, created_at `2025-01-01 00:00:00`,
updated_at NULL. This is a replacement, not a recovery of the original deleted
row. It is included in the checksum above. No records changed during measurement.

### Original indexes

Actual `SHOW INDEX FROM designs` returned only:

| Key | Unique | Column | Sequence | Type | Cardinality estimate |
|---|---|---|---|---|---|
| PRIMARY | Yes | id | 1 | BTREE | 99510 |
| designs_category_id_foreign | No | category_id | 1 | BTREE | 22 |

The category index supports the foreign key. No title, status, creation-date,
or custom composite performance index exists at baseline.

### Measurement method

Each query is prepared once on the existing PDO connection. One execution and
full fetch warms it; then ten sequential executions are timed with `hrtime(true)`
around `execute()` plus `fetchAll(PDO::FETCH_ASSOC)`. Values below are milliseconds.
The median is the average of the fifth and sixth sorted measurements.

This measures database execution plus local client transfer/fetch overhead.
It excludes application bootstrap, query building, statement preparation,
Eloquent hydration, pagination count queries, category eager loading, Blade,
HTTP and images. Matched counts and EXPLAIN are collected outside the timed loop.
These are warm-run local measurements: no server restart, cache flush, cold-cache
claim, or controlled background-load isolation. The variation is retained in raw
values; repeat the identical method, parameters and dataset after indexing.

### Category and status, newest first

Category 13 is Branding. Actual matched count: **4168**; returned rows: **24**.

```sql
select * from `designs` where `category_id` = ? and `status` = ? order by `created_at` desc, `id` desc limit 24 offset 0
```

Bindings: `[13, "published"]`.

Actual `EXPLAIN` (using the same SQL and bindings):

```json
{"id":1,"select_type":"SIMPLE","table":"designs","type":"ref","possible_keys":"designs_category_id_foreign","key":"designs_category_id_foreign","key_len":"8","ref":"const","rows":"15162","Extra":"Using where; Using filesort"}
```

Raw ms: `38.1568, 24.9078, 36.0765, 166.0931, 125.7464, 35.1834, 22.5797, 26.5224, 31.4558, 27.8361`.

Median: **33.3196 ms**.

### Title prefix, newest first

Search input: `Design sample 099`. Actual matched count: **500**; returned rows: **24**.

```sql
select * from `designs` where title LIKE ? ESCAPE '!' order by `created_at` desc, `id` desc limit 24 offset 0
```

Bindings: `["Design sample 099%"]`.

Actual `EXPLAIN`:

```json
{"id":1,"select_type":"SIMPLE","table":"designs","type":"ALL","possible_keys":null,"key":null,"key_len":null,"ref":null,"rows":"99510","Extra":"Using where; Using filesort"}
```

Raw ms: `77.6367, 65.5883, 84.1416, 141.7664, 97.4326, 106.7879, 110.8545, 121.8405, 116.0460, 113.7731`.

Median: **108.8212 ms**.

### Interpretation and verification notes

The category/status query uses the existing category foreign-key index, then
filters and sorts. Prefix search scans the table and sorts. EXPLAIN row values
are optimizer estimates, not measured rows visited. `EXPLAIN ANALYZE` was NOT RUN;
plain EXPLAIN satisfies this baseline. Index selection and remeasurement belong
to Phase 09.

An initial Tinker command failed from shell quoting before collecting evidence.
The script's first metadata attempt failed because MariaDB rejected a bound
placeholder in `SHOW TABLE STATUS LIKE ?`; the fixed literal table-name command
then succeeded. The successful run above is the baseline source.

Quick Phase 06/07 regression review: `php artisan test` passed 36 tests / 209
assertions (2.89s), and `vendor\bin\pint --test` passed before the benchmark script
was added. No regression was observed in those checks.

## After indexes — 2026-10-29 (Phase 09)

Migration: `2026_10_29_160000_add_design_performance_indexes.php`.
Applied successfully using:

```powershell
php artisan migrate --path=database/migrations/2026_10_29_160000_add_design_performance_indexes.php
php scripts/performance-baseline.php
```

The same benchmark script was run immediately before and after migration.
Exact count remained 100000, checksum remained **3289254657**, matched counts
remained 4168 and 500, and both queries still returned 24 rows. SQL, bindings,
offset, sorting, PHP/DB versions, buffer pool, collation, query cache OFF and
measurement method were unchanged from Phase 08. The machine was the same.
Background load was not controlled; these warm local runs are illustrative,
not a production latency guarantee. Index creation itself warms database pages.

### Index choice and actual SHOW INDEX result

The composite `(category_id, status, created_at, id)` places the two equality
predicates first and the ordering columns next. With both first columns fixed,
the server can traverse the index backwards for the descending order and LIMIT.
The title BTREE supports the confirmed literal prefix search. A title range
does not also provide global creation-date ordering, so filesort can remain.

Actual post-migration `SHOW INDEX` returned the following BTREE entries (all
ascending, no prefix truncation, non-null columns):

| Key | Unique | Sequence | Column | Cardinality estimate |
|---|---|---|---|---|
| PRIMARY | Yes | 1 | id | 99510 |
| designs_category_status_created_id_index | No | 1 | category_id | 22 |
| designs_category_status_created_id_index | No | 2 | status | 46 |
| designs_category_status_created_id_index | No | 3 | created_at | 99510 |
| designs_category_status_created_id_index | No | 4 | id | 99510 |
| designs_title_index | No | 1 | title | 99510 |

MariaDB automatically removed the redundant, automatically created
`designs_category_id_foreign` index when the composite became available to
support the FK. The migration does not drop the FK or PK. Its `down()` recreates
the standalone category index when absent before dropping the composite, so
FK support is retained. Rollback and reapplication were subsequently verified
on a separate local database; see the follow-up below.

### Actual post-index EXPLAIN

Category/status, exact SQL and bindings from the baseline:

```json
{"id":1,"select_type":"SIMPLE","table":"designs","type":"ref","possible_keys":"designs_category_status_created_id_index","key":"designs_category_status_created_id_index","key_len":"9","ref":"const,const","rows":"4168","Extra":"Using where"}
```

Prefix-title, exact SQL and binding from the baseline:

```json
{"id":1,"select_type":"SIMPLE","table":"designs","type":"range","possible_keys":"designs_title_index","key":"designs_title_index","key_len":"1022","ref":null,"rows":"500","Extra":"Using index condition; Using filesort"}
```

EXPLAIN ANALYZE was NOT RUN. Estimates are not actual visited-row counts.

### Raw measurements and comparison

The immediate pre-migration rerun (same original plans and indexes) recorded:

- Category/status ms: `37.5944, 30.4656, 38.1780, 28.4277, 33.5166, 38.4311, 35.0648, 218.7172, 109.5546, 39.2378`; median **37.8862**.
- Prefix ms: `98.2005, 123.8229, 127.4132, 108.8691, 133.1295, 108.2347, 127.1120, 128.7318, 129.8551, 131.0868`; median **127.2626**.

Post-migration values:

- Category/status ms: `0.9247, 0.9181, 0.8570, 0.7753, 1.1447, 1.3012, 0.8111, 0.8278, 1.0035, 0.9255`; median **0.9214**.
- Prefix ms: `2.3521, 2.1372, 3.0115, 3.2940, 2.0098, 1.0330, 2.4949, 2.3130, 2.4398, 2.4261`; median **2.3891**.

| Query | Phase 08 median ms | Immediate pre-index median ms | After median ms |
|---|---|---|---|
| Category/status | 33.3196 | 37.8862 | 0.9214 |
| Title prefix | 108.8212 | 127.2626 | 2.3891 |

Both measured queries improved locally; no regression was observed for these
two fixed predicates. Category/status no longer reports filesort. Prefix search
still reports filesort, but narrows the estimated candidate set from 99510 to
500. Broad prefixes can be less selective and the optimizer may choose a scan;
these results do not establish speedups for every prefix/filter combination.
No end-to-end HTTP or pagination COUNT speedup is claimed.

### Storage, writes and scaling

Reported index storage increased from 3686400 to **12107776 bytes**, a net
increase of **8421376 bytes** (approximately 8.03 MiB). Data length remained
11026432 bytes. Allocation and statistics are server metadata, not exact
per-index payload measurements. Extra indexes cost storage and buffer-pool
space, and must be maintained during insert/delete and indexed-column updates;
write throughput was NOT benchmarked.

At one million rows, deep OFFSET still walks/skips many matching entries and
COUNT can dominate pagination. Consider keyset/cursor pagination using
`(created_at, id)` when numbered pages are unnecessary; it changes the UI's
pagination contract and is not implemented here. COUNT caching or avoiding total
counts requires an explicit consistency/product choice. Recheck selectivity,
plans, buffer-pool sizing and realistic Arabic/English workloads on actual data.
A dedicated search engine is only a future option if requirements expand to
fuzzy/full-text search; the current project keeps MySQL prefix search.

Verification: forward migration passed; `php artisan test` passed **36 tests /
209 assertions**, duration 2.29s; `vendor\bin\pint --test` and `git diff --check`
passed. No application query semantics were changed.

### Rollback verification follow-up

`php scripts/verify-index-rollback.php` passed on separate database
`dorra_index_verify_20261029_115223`, using a single design and category fixture.
The script creates a new timestamp-named database using the local connection,
then explicitly selects it for all migration and fixture operations. The
database is retained for inspection; no cleanup/drop was performed.

- Initial migration exit: 0; composite and title indexes present.
- Targeted `migrate:rollback --step=1 --path=database/migrations/2026_10_29_160000_add_design_performance_indexes.php` exit: 0.
- After rollback, actual `SHOW INDEX` showed only PRIMARY(id) and `designs_category_id_foreign(category_id)`.
- Attempted deletion of the used fixture category was rejected with MariaDB FK error 1451, verifying FK enforcement after rollback.
- Targeted migration reapplication exit: 0; composite and title indexes present again.
- Fixture count remained 1; checksum before, after rollback, and after reapplication: **3758889467**.
- After reapplication MariaDB retained the now-explicit standalone category index as well as the composite. This differs from its removal of the original automatically generated FK index on the first application; rollback correctness is unaffected.

Browser smoke follow-up: NOT RUN. The browser tool returned
`Local browser use is disabled in Work settings`. Search/filter/CRUD browser
journeys still need manual verification or enabled browser access.
