<?php

namespace Alagunto\EzSchedules\Test;

use Alagunto\EzSchedules\SchedulesProvider;
use Illuminate\Support\Facades\DB;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app) {
        $app['config']->set('database.default', 'test');

        $app['config']->set('database.connections.test', [
            'driver'    => 'sqlite',
            'database'  => 'test.db',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);
    }

    protected function getPackageAliases($app) {
        return [
            'config' => 'Illuminate\Config\Repository'
        ];
    }

    protected function getPackageProviders($app)
    {
        return [SchedulesProvider::class];
    }

    protected function setUp() {
        parent::setUp();

        if(!file_exists(__DIR__ . "/../test.db"))
            touch(__DIR__ . "/../test.db");


        $this->loadLaravelMigrations();
        $this->artisan("migrate");
        DB::beginTransaction();
    }

    protected function tearDown() {
        DB::rollback();
        parent::tearDown();
    }
}
