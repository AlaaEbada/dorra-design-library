<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // Runs before RefreshDatabase or any other database test trait.
        $this->ensureIsolatedDatabase($app);

        return $app;
    }

    protected function ensureIsolatedDatabase(Application $app): void
    {
        $config = $app['config'];
        $connection = $config->get('database.connections.sqlite');

        if (! $app->environment('testing')
            || $config->get('app.env') !== 'testing'
            || $config->get('database.default') !== 'sqlite'
            || ! is_array($connection)
            || ($connection['driver'] ?? null) !== 'sqlite'
            || ($connection['database'] ?? null) !== ':memory:'
            || ! empty($connection['url'])
            || isset($connection['read'])
            || isset($connection['write'])) {
            throw new LogicException('Tests require the testing environment and isolated SQLite :memory: database.');
        }

        // Explicit named connections must not reach the development database either.
        $config->set('database.connections', ['sqlite' => $connection]);
    }
}
