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
            DB::statement('CREATE TEMPORARY TABLE transaction_sell_lines_purchase_lines_backup AS SELECT * FROM transaction_sell_lines_purchase_lines');
            Schema::drop('transaction_sell_lines_purchase_lines');

            Schema::create('transaction_sell_lines_purchase_lines', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('sell_line_id')->nullable();
                $table->integer('stock_adjustment_line_id')->nullable();
                $table->integer('purchase_line_id');
                $table->decimal('quantity', 22, 4);
                $table->decimal('qty_returned', 22, 4)->default(0);
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_sell_lines_purchase_lines_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            // Insert data from backup
            DB::statement("INSERT INTO transaction_sell_lines_purchase_lines ($column_list) SELECT $column_list FROM transaction_sell_lines_purchase_lines_backup");
            DB::statement('DROP TABLE transaction_sell_lines_purchase_lines_backup');
        } else {
            Schema::table('transaction_sell_lines_purchase_lines', function (Blueprint $table) {
                $table->bigIncrements('id')->change();
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
            DB::statement('CREATE TEMPORARY TABLE transaction_sell_lines_purchase_lines_backup AS SELECT * FROM transaction_sell_lines_purchase_lines');
            Schema::drop('transaction_sell_lines_purchase_lines');

            Schema::create('transaction_sell_lines_purchase_lines', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('sell_line_id')->nullable();
                $table->integer('stock_adjustment_line_id')->nullable();
                $table->integer('purchase_line_id');
                $table->decimal('quantity', 22, 4);
                $table->decimal('qty_returned', 22, 4)->default(0);
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_sell_lines_purchase_lines_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            // Insert data from backup
            DB::statement("INSERT INTO transaction_sell_lines_purchase_lines ($column_list) SELECT $column_list FROM transaction_sell_lines_purchase_lines_backup");
            DB::statement('DROP TABLE transaction_sell_lines_purchase_lines_backup');
        } else {
            Schema::table('transaction_sell_lines_purchase_lines', function (Blueprint $table) {
                $table->increments('id')->change();
            });
        }
    }
};
