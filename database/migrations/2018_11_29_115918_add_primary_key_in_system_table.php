<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create a temporary table with all existing data
        DB::statement('CREATE TEMPORARY TABLE system_backup AS SELECT * FROM system');
        
        // Drop the original table
        Schema::drop('system');
        
        // Recreate the table with the primary key
        Schema::create('system', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('value');
        });

        // Restore the data
        DB::statement('INSERT INTO system (key, value) SELECT key, value FROM system_backup');
        
        // Drop the temporary table
        DB::statement('DROP TABLE system_backup');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
