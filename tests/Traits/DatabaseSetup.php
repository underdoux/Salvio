<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait DatabaseSetup
{
    protected function setupDatabase()
    {
        // Drop all tables
        Schema::disableForeignKeyConstraints();

        // Get all tables
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        foreach ($tables as $table) {
            if ($table->name !== 'sqlite_sequence') {
                Schema::drop($table->name);
            }
        }

        Schema::enableForeignKeyConstraints();

        // Run migrations
        $this->artisan('migrate:fresh', [
            '--path' => [
                'database/migrations/0001_01_01_000000_create_users_table.php',
                'database/migrations/2025_04_30_065714_create_permission_tables.php',
                'database/migrations/2025_04_30_100000_create_settings_table.php'
            ]
        ]);
    }
}
