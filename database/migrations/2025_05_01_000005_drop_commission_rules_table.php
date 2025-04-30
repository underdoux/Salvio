<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('commission_rules');
    }

    public function down()
    {
        // No need to recreate the table as it will be created by the other migration
    }
};
