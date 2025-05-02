<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

trait RefreshDatabaseWithoutVacuum
{
    use RefreshDatabase;

    protected function refreshTestDatabase()
    {
        if (!Schema::hasTable('migrations')) {
            $this->artisan('migrate:install');
        }

        $this->artisan('migrate:fresh', [
            '--path' => [
                'database/migrations',
                'vendor/laravel/framework/src/Illuminate/Auth/Passwords/migrations',
                'vendor/laravel/framework/src/Illuminate/Database/Migrations/migrations'
            ],
            '--realpath' => true
        ]);
    }
}
