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
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            DB::statement('CREATE TEMPORARY TABLE cash_register_transactions_backup AS SELECT * FROM cash_register_transactions');
            Schema::drop('cash_register_transactions');

            Schema::create('cash_register_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('cash_register_id');
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('pay_method')->nullable();
                $table->string('type');
                $table->string('transaction_type')->nullable();
                $table->integer('transaction_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(cash_register_transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            // Insert data from backup
            DB::statement("INSERT INTO cash_register_transactions ($column_list) SELECT $column_list FROM cash_register_transactions_backup");
            DB::statement('DROP TABLE cash_register_transactions_backup');
        } else {
            Schema::table('cash_register_transactions', function (Blueprint $table) {
                $table->string('transaction_type')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table
            DB::statement('CREATE TEMPORARY TABLE cash_register_transactions_backup AS SELECT * FROM cash_register_transactions');
            Schema::drop('cash_register_transactions');

            Schema::create('cash_register_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('cash_register_id');
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('pay_method')->nullable();
                $table->string('type');
                $table->enum('transaction_type', ['sell', 'refund'])->nullable();
                $table->integer('transaction_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(cash_register_transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            // Insert data from backup
            DB::statement("INSERT INTO cash_register_transactions ($column_list) SELECT $column_list FROM cash_register_transactions_backup");
            DB::statement('DROP TABLE cash_register_transactions_backup');
        } else {
            Schema::table('cash_register_transactions', function (Blueprint $table) {
                $table->enum('transaction_type', ['sell', 'refund'])->nullable()->change();
            });
        }
    }
};
