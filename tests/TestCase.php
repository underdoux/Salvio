<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure no active transactions
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Run migrations for spatie/laravel-permission
        Artisan::call('migrate', ['--path' => 'vendor/spatie/laravel-permission/database/migrations']);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Run migrations for our application
        $this->artisan('migrate');
    }

    protected function tearDown(): void
    {
        // Ensure no active transactions are left
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }
}
