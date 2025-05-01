<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

trait DatabaseMigrations
{
    protected function runCustomMigrations()
    {
        // Drop all tables first
        Schema::disableForeignKeyConstraints();

        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        foreach ($tables as $table) {
            if ($table->name !== 'sqlite_sequence') {
                Schema::dropIfExists($table->name);
            }
        }

        Schema::enableForeignKeyConstraints();

        // Run only specific migrations
        $this->artisan('migrate', [
            '--path' => [
                'database/migrations/0001_01_01_000000_create_users_table.php',
                'database/migrations/0001_01_01_000001_create_cache_table.php',
                'database/migrations/2025_04_30_065714_create_permission_tables.php',
                'database/migrations/2025_04_30_100000_create_settings_table.php'
            ]
        ]);

        // Clear cache
        Artisan::call('cache:clear');
    }
}
