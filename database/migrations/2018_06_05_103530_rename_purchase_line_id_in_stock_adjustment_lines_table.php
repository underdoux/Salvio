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
        // Create new stock_adjustment_lines table with updated schema
        Schema::create('new_stock_adjustment_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('variation_id')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->decimal('unit_price', 22, 4)->comment('Last purchase unit price')->nullable();
            $table->integer('removed_purchase_line')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
        });

        // Copy data from old table and rename purchase_line_id to removed_purchase_line
        DB::statement('INSERT INTO new_stock_adjustment_lines (
            id, transaction_id, product_id, variation_id, quantity, unit_price, removed_purchase_line, created_at, updated_at
        ) SELECT 
            id, transaction_id, product_id, variation_id, quantity, unit_price, purchase_line_id, created_at, updated_at
        FROM stock_adjustment_lines');

        // Drop old table and rename new table
        Schema::dropIfExists('stock_adjustment_lines');
        Schema::rename('new_stock_adjustment_lines', 'stock_adjustment_lines');
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
