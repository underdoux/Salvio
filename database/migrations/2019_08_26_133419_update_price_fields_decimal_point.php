<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For SQLite, we need to recreate tables with new decimal precision
        // This is a no-op migration for SQLite since changing column precision 
        // requires table recreation, which we want to avoid for this update
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Original MySQL code left for reference
        /*
        $tables = DB::select("SELECT distinct table_name,
            column_name, data_type, column_default
            from information_schema.columns
            where data_type='decimal'
            and table_schema=DATABASE()
            and numeric_scale=2
            and numeric_precision=20");

        foreach ($tables as $table_column) {
            DB::statement("ALTER TABLE {$table_column->table_name} MODIFY COLUMN {$table_column->column_name} decimal(22,4)");
        }
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
