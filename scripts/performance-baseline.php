<?php

use App\Models\Design;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! $app->environment('local') || DB::connection()->getDriverName() !== 'mysql') {
    throw new RuntimeException('Requires a local MySQL-compatible sample database.');
}

DB::connection()->disableQueryLog();
$pdo = DB::connection()->getPdo();
$pdo->exec('SET TRANSACTION READ ONLY');
$pdo->beginTransaction();

try {
    $count = DB::table('designs')->count();
    if ($count !== 100000) {
        throw new RuntimeException('Expected exactly 100000 sample designs.');
    }

    $report = [
        'php' => PHP_VERSION,
        'laravel' => $app->version(),
        'os' => php_uname(),
        'database' => DB::connection()->getDatabaseName(),
        'count' => $count,
        'fingerprint' => DB::select('CHECKSUM TABLE designs'),
        'server' => DB::selectOne('SELECT VERSION() AS version, @@sql_mode AS sql_mode, @@innodb_buffer_pool_size AS buffer_pool_bytes, @@query_cache_type AS query_cache_type, @@query_cache_size AS query_cache_bytes, @@collation_connection AS collation_connection'),
        'table' => DB::select("SHOW TABLE STATUS LIKE 'designs'"),
        'columns' => DB::select('SHOW FULL COLUMNS FROM designs'),
        'indexes' => DB::select('SHOW INDEX FROM designs'),
        'queries' => [],
    ];

    foreach ([
        'category_status' => ['category_id' => 13, 'status' => 'published'],
        'prefix_title' => ['title' => 'Design sample 099'],
    ] as $name => $filters) {
        $query = Design::query()->filter($filters)->orderByDesc('created_at')->orderByDesc('id');
        $matched = (clone $query)->count();
        $query->limit(24)->offset(0);
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $plan = DB::select('EXPLAIN '.$sql, $bindings);
        $statement = $pdo->prepare($sql);
        $statement->execute($bindings);
        $statement->fetchAll(PDO::FETCH_ASSOC);
        $times = [];
        for ($iteration = 0; $iteration < 10; $iteration++) {
            $started = hrtime(true);
            $statement->execute($bindings);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $times[] = round((hrtime(true) - $started) / 1e6, 4);
        }
        $sorted = $times;
        sort($sorted);
        $report['queries'][$name] = [
            'filters' => $filters,
            'matched' => $matched,
            'sql' => $sql,
            'bindings' => $bindings,
            'explain' => $plan,
            'returned' => count($rows),
            'milliseconds' => $times,
            'median_ms' => ($sorted[4] + $sorted[5]) / 2,
        ];
    }
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL;
} finally {
    $pdo->rollBack();
}
