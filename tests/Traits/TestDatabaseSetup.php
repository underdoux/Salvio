<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

trait TestDatabaseSetup
{
    protected function setUp(): void
    {
        parent::setUp();

        // Drop commission_rules table if it exists
        if (Schema::hasTable('commission_rules')) {
            Schema::dropIfExists('commission_rules');
        }

        // Run migrations
        $this->artisan('migrate:fresh');
    }

    protected function tearDown(): void
    {
        // Clean up
        Schema::dropIfExists('commission_rules');
        parent::tearDown();
    }
}
