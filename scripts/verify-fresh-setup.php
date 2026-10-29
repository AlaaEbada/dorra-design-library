<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! $app->environment('local') || DB::connection()->getDriverName() !== 'mysql') {
    throw new RuntimeException('Requires local MySQL-compatible configuration.');
}

$database = 'dorra_handoff_verify_'.gmdate('Ymd_His');
DB::statement('CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$connection = config('database.connections.mysql');
$connection['url'] = null;
$connection['database'] = $database;
config(['database.connections.handoff_verification' => $connection, 'database.default' => 'handoff_verification']);
DB::purge('handoff_verification');

$report = ['database' => DB::connection()->getDatabaseName()];
foreach (['migrate' => ['--database' => 'handoff_verification', '--force' => true], 'db:seed' => ['--database' => 'handoff_verification', '--class' => 'DesignLibrarySeeder', '--force' => true]] as $command => $arguments) {
    $exit = Artisan::call($command, $arguments);
    $report[$command] = ['exit' => $exit, 'output' => Artisan::output()];
    if ($exit !== 0) {
        throw new RuntimeException(Artisan::output());
    }
}
$report['designs'] = DB::table('designs')->count();
$report['categories'] = DB::table('categories')->count();
$report['statuses'] = DB::table('designs')->selectRaw('status, count(*) as total')->groupBy('status')->get();
$report['accounts'] = [];
foreach (['admin', 'viewer'] as $role) {
    $account = DB::table('users')->where('email', $role.'@dorra.test')->first();
    $valid = $account && $account->role === $role && Hash::check('DorraSample123!', $account->password);
    $report['accounts'][$role] = $valid;
    if (! $valid) {
        throw new RuntimeException('Sample account verification failed.');
    }
}
if ($report['designs'] !== 100000 || $report['categories'] !== 12) {
    throw new RuntimeException('Unexpected sample dataset counts.');
}
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
