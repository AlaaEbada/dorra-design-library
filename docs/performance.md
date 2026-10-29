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
