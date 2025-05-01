<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

trait HandleCommissionRules
{
    protected function dropCommissionRules()
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('commission_rules')) {
            Schema::dropIfExists('commission_rules');
        }

        Schema::enableForeignKeyConstraints();
    }

    protected function cleanupCommissionRules()
    {
        $this->dropCommissionRules();
        DB::disconnect();
    }
}
