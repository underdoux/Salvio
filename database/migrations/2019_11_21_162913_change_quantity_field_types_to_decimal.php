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
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate tables to modify column types
            
            // purchase_lines table
            DB::statement('CREATE TEMPORARY TABLE purchase_lines_backup AS SELECT * FROM purchase_lines');
            Schema::drop('purchase_lines');
            Schema::create('purchase_lines', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->unsigned();
                $table->integer('product_id')->unsigned();
                $table->integer('variation_id')->unsigned();
                $table->decimal('quantity', 22, 4)->default(0);
                $table->decimal('pp_without_discount', 22, 4)->default(0);
                $table->decimal('discount_percent', 22, 4)->default(0);
                $table->decimal('purchase_price', 22, 4);
                $table->decimal('purchase_price_inc_tax', 22, 4)->default(0);
                $table->decimal('item_tax', 22, 4);
                $table->integer('tax_id')->unsigned()->nullable();
                $table->decimal('quantity_sold', 22, 4)->default(0);
                $table->decimal('quantity_adjusted', 22, 4)->default(0);
                $table->decimal('quantity_returned', 22, 4)->default(0);
                $table->decimal('mfg_quantity_used', 22, 4)->default(0);
                $table->integer('mfg_date')->nullable();
                $table->integer('exp_date')->nullable();
                $table->string('lot_number')->nullable();
                $table->integer('sub_unit_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table excluding sub_unit_id
            $columns = DB::select("PRAGMA table_info(purchase_lines_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            $column_names = array_filter($column_names, function($col) {
                return $col !== 'sub_unit_id';
            });
            $column_list = implode(', ', $column_names);

            // Restore the data
            DB::statement("INSERT INTO purchase_lines ($column_list) SELECT $column_list FROM purchase_lines_backup");
            DB::statement('DROP TABLE purchase_lines_backup');

            // transaction_sell_lines table
            DB::statement('CREATE TEMPORARY TABLE transaction_sell_lines_backup AS SELECT * FROM transaction_sell_lines');
            Schema::drop('transaction_sell_lines');
            Schema::create('transaction_sell_lines', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->unsigned();
                $table->integer('product_id')->unsigned();
                $table->integer('variation_id')->unsigned();
                $table->decimal('quantity', 22, 4)->default(0);
                $table->decimal('unit_price', 22, 4);
                $table->decimal('unit_price_inc_tax', 22, 4);
                $table->decimal('item_tax', 22, 4);
                $table->integer('tax_id')->unsigned()->nullable();
                $table->integer('parent_sell_line_id')->nullable();
                $table->string('lot_no_line_id')->nullable();
                $table->decimal('line_discount_type', 22, 4)->default(0);
                $table->decimal('line_discount_amount', 22, 4)->default(0);
                $table->decimal('unit_price_before_discount', 22, 4)->default(0);
                $table->decimal('quantity_returned', 22, 4)->default(0);
                $table->string('sell_line_note')->nullable();
                $table->integer('sub_unit_id')->nullable();
                $table->integer('discount_id')->nullable();
                $table->integer('res_service_staff_id')->nullable();
                $table->string('res_line_order_status')->nullable();
                $table->string('children_type')->default('');
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_sell_lines_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            $column_list = implode(', ', $column_names);

            // Restore the data
            DB::statement("INSERT INTO transaction_sell_lines ($column_list) SELECT $column_list FROM transaction_sell_lines_backup");
            DB::statement('DROP TABLE transaction_sell_lines_backup');

            // stock_adjustment_lines table
            DB::statement('CREATE TEMPORARY TABLE stock_adjustment_lines_backup AS SELECT * FROM stock_adjustment_lines');
            Schema::drop('stock_adjustment_lines');
            Schema::create('stock_adjustment_lines', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->unsigned();
                $table->integer('product_id')->unsigned();
                $table->integer('variation_id')->unsigned();
                $table->decimal('quantity', 22, 4);
                $table->decimal('unit_price', 22, 4)->comment("Last purchase unit price")->nullable();
                $table->string('removed_purchase_line')->nullable();
                $table->string('lot_no_line_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(stock_adjustment_lines_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            $column_list = implode(', ', $column_names);

            // Restore the data
            DB::statement("INSERT INTO stock_adjustment_lines ($column_list) SELECT $column_list FROM stock_adjustment_lines_backup");
            DB::statement('DROP TABLE stock_adjustment_lines_backup');

        } else {
            DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN quantity DECIMAL(22,4) NOT NULL DEFAULT '0'");
            DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN quantity DECIMAL(22,4) NOT NULL DEFAULT '0'");
            DB::statement("ALTER TABLE stock_adjustment_lines MODIFY COLUMN quantity DECIMAL(22,4) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to reverse as decimal precision changes are not critical
    }
};
