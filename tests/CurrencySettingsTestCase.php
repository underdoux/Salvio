<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class CurrencySettingsTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Drop commission_rules table if it exists
        if (Schema::hasTable('commission_rules')) {
            Schema::dropIfExists('commission_rules');
        }

        // Run only the migrations we need
        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/0001_01_01_000000_create_users_table.php',
                'database/migrations/2025_04_30_065714_create_permission_tables.php',
                'database/migrations/2025_04_30_100000_create_settings_table.php'
            ]
        ]);
    }

    protected function tearDown(): void
    {
        // Drop commission_rules table if it exists
        if (Schema::hasTable('commission_rules')) {
            Schema::dropIfExists('commission_rules');
        }

        parent::tearDown();
    }
}
