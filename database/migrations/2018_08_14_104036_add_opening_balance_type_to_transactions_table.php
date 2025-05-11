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
        // Create new table with updated schema
        Schema::create('transactions_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type');
            $table->string('status');
            $table->string('payment_status');
            $table->string('adjustment_type')->nullable();
            $table->integer('contact_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->datetime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0);
            $table->integer('tax_id')->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('expense_category_id')->nullable();
            $table->integer('expense_for')->nullable();
            $table->integer('commission_agent')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('location_id')->nullable();
            $table->decimal('exchange_rate', 22, 4)->default(1);
            $table->decimal('total_amount_recovered', 22, 4)->nullable();
            $table->integer('transfer_parent_id')->nullable();
            $table->integer('opening_stock_product_id')->nullable();
            $table->string('res_order_status')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Copy data from old table
        DB::statement('INSERT INTO transactions_new SELECT * FROM transactions');

        // Drop old table
        Schema::drop('transactions');

        // Rename new table
        Schema::rename('transactions_new', 'transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create old table
        Schema::create('transactions_old', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->enum('type', ['purchase', 'sell', 'expense', 'stock_adjustment', 'sell_transfer', 'purchase_transfer', 'opening_stock', 'sell_return']);
            $table->string('status');
            $table->string('payment_status');
            $table->string('adjustment_type')->nullable();
            $table->integer('contact_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->datetime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0);
            $table->integer('tax_id')->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('expense_category_id')->nullable();
            $table->integer('expense_for')->nullable();
            $table->integer('commission_agent')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('location_id')->nullable();
            $table->decimal('exchange_rate', 22, 4)->default(1);
            $table->decimal('total_amount_recovered', 22, 4)->nullable();
            $table->integer('transfer_parent_id')->nullable();
            $table->integer('opening_stock_product_id')->nullable();
            $table->string('res_order_status')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Copy data back
        DB::statement('INSERT INTO transactions_old SELECT * FROM transactions');

        // Drop new table
        Schema::drop('transactions');

        // Rename old table back
        Schema::rename('transactions_old', 'transactions');
    }
};
