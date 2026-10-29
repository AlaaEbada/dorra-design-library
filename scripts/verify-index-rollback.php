<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! $app->environment('local') || DB::connection()->getDriverName() !== 'mysql') {
    throw new RuntimeException('Requires local MySQL-compatible configuration.');
}

$sourceDatabase = DB::connection()->getDatabaseName();
$verificationDatabase = 'dorra_index_verify_'.gmdate('Ymd_His');
DB::statement('CREATE DATABASE `'.$verificationDatabase.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$connection = config('database.connections.mysql');
$connection['url'] = null;
$connection['database'] = $verificationDatabase;
config(['database.connections.index_verification' => $connection, 'database.default' => 'index_verification']);
DB::purge('index_verification');

if (DB::connection()->getDatabaseName() === $sourceDatabase) {
    throw new RuntimeException('Verification database isolation failed.');
}

$report = ['source_database' => $sourceDatabase, 'verification_database' => $verificationDatabase];
$report['migrate_exit'] = Artisan::call('migrate', ['--database' => 'index_verification', '--force' => true]);
$report['migrate_output'] = Artisan::output();
if ($report['migrate_exit'] !== 0) {
    throw new RuntimeException($report['migrate_output']);
}

$categoryId = DB::table('categories')->insertGetId(['name' => 'Rollback fixture']);
DB::table('designs')->insert([
    'title' => 'Rollback fixture design',
    'category_id' => $categoryId,
    'status' => 'draft',
    'thumbnail' => 'images/design-placeholder.svg',
    'created_at' => '2025-01-01 00:00:00',
]);
$report['indexes_before'] = DB::select('SHOW INDEX FROM designs');
$report['checksum_before'] = DB::select('CHECKSUM TABLE designs');
$report['rollback_exit'] = Artisan::call('migrate:rollback', [
    '--database' => 'index_verification',
    '--path' => 'database/migrations/2026_10_29_160000_add_design_performance_indexes.php',
    '--step' => 1,
    '--force' => true,
]);
$report['rollback_output'] = Artisan::output();
if ($report['rollback_exit'] !== 0) {
    throw new RuntimeException($report['rollback_output']);
}
$report['indexes_rolled_back'] = DB::select('SHOW INDEX FROM designs');
$report['checksum_rolled_back'] = DB::select('CHECKSUM TABLE designs');
$foreignKeyRejected = false;
try {
    DB::table('categories')->where('id', $categoryId)->delete();
} catch (QueryException $exception) {
    if (($exception->errorInfo[1] ?? null) !== 1451) {
        throw $exception;
    }
    $foreignKeyRejected = true;
}
$report['foreign_key_restricts_delete_after_rollback'] = $foreignKeyRejected;
if (! $foreignKeyRejected) {
    throw new RuntimeException('Foreign key restriction failed.');
}
$report['reapply_exit'] = Artisan::call('migrate', [
    '--database' => 'index_verification',
    '--path' => 'database/migrations/2026_10_29_160000_add_design_performance_indexes.php',
    '--force' => true,
]);
$report['reapply_output'] = Artisan::output();
if ($report['reapply_exit'] !== 0) {
    throw new RuntimeException($report['reapply_output']);
}
$report['indexes_reapplied'] = DB::select('SHOW INDEX FROM designs');
$report['checksum_reapplied'] = DB::select('CHECKSUM TABLE designs');
$report['fixture_count'] = DB::table('designs')->count();
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
