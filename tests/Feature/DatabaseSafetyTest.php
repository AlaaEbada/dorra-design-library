<?php

namespace Tests\Feature;

use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DatabaseSafetyTest extends TestCase
{
    public function test_only_an_in_memory_sqlite_connection_is_available(): void
    {
        $this->assertTrue($this->app->environment('testing'));
        $this->assertSame(['sqlite'], array_keys(config('database.connections')));
        $connection = $this->app['db']->connection();
        $this->assertSame('sqlite', $connection->getDriverName());
        $this->assertSame(':memory:', $connection->getDatabaseName());
        $this->assertSame('', $connection->selectOne('PRAGMA database_list')->file);
    }

    #[DataProvider('unsafeConfigurations')]
    public function test_unsafe_configuration_is_rejected_before_database_refresh(string $key, mixed $value): void
    {
        config([$key => $value]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('isolated SQLite :memory:');
        $this->ensureIsolatedDatabase($this->app);
    }

    public static function unsafeConfigurations(): array
    {
        return [
            'development environment' => ['app.env', 'local'],
            'development connection' => ['database.default', 'mysql'],
            'non-sqlite driver' => ['database.connections.sqlite.driver', 'mysql'],
            'file database' => ['database.connections.sqlite.database', 'development.sqlite'],
            'URL override' => ['database.connections.sqlite.url', 'sqlite:///development.sqlite'],
            'read override' => ['database.connections.sqlite.read', ['database' => 'development.sqlite']],
            'write override' => ['database.connections.sqlite.write', ['database' => 'development.sqlite']],
        ];
    }
}
