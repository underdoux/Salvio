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
        // Create new business table with updated schema
        Schema::create('new_business', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('currency_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->string('tax_number_1', 100)->nullable();
            $table->string('tax_label_1', 10)->nullable();
            $table->string('tax_number_2')->nullable();
            $table->string('tax_label_2')->nullable();
            $table->float('default_profit_percent', 5, 2)->default(0);
            $table->integer('owner_id')->unsigned();
            $table->string('time_zone')->default('Asia/Kolkata');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->string('accounting_method')->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable();
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('includes');
            $table->string('logo')->nullable();
            $table->string('sku_prefix')->nullable();
            $table->boolean('enable_product_expiry')->default(0);
            $table->string('expiry_type')->default('add_expiry')->nullable();
            $table->string('on_product_expiry')->check("on_product_expiry IN ('keep_selling', 'stop_selling', 'auto_delete')")->default('keep_selling');
            $table->integer('stop_selling_before')->default(0);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        // Copy data from old table with explicit columns
        DB::statement('INSERT INTO new_business (
            id, name, currency_id, start_date, tax_number_1, tax_label_1, tax_number_2, tax_label_2,
            default_profit_percent, owner_id, time_zone, fy_start_month, accounting_method,
            default_sales_discount, sell_price_tax, logo, sku_prefix, enable_product_expiry,
            expiry_type, on_product_expiry, stop_selling_before, created_at, updated_at
        ) SELECT 
            id, name, currency_id, start_date, tax_number_1, tax_label_1, tax_number_2, tax_label_2,
            default_profit_percent, owner_id, time_zone, fy_start_month, accounting_method,
            default_sales_discount, sell_price_tax, logo, sku_prefix, enable_product_expiry,
            expiry_type, on_product_expiry, stop_selling_before, created_at, updated_at
        FROM business');

        // Drop old table and rename new table
        Schema::dropIfExists('business');
        Schema::rename('new_business', 'business');

        // Create new transactions table with updated schema
        Schema::create('new_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type')->check("type IN ('purchase', 'sell', 'expense', 'stock_adjustment', 'sell_transfer', 'purchase_transfer', 'opening_stock')");
            $table->string('status')->check("status IN ('received', 'pending', 'ordered', 'draft', 'final')");
            $table->string('payment_status')->check("payment_status IN ('paid', 'due', 'partial')");
            $table->string('adjustment_type')->nullable()->check("adjustment_type IN ('normal', 'abnormal')");
            $table->integer('contact_id')->unsigned()->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0);
            $table->integer('tax_id')->unsigned()->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->string('discount_type')->nullable()->check("discount_type IN ('fixed', 'percentage')");
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('expense_category_id')->nullable()->unsigned();
            $table->integer('expense_for')->nullable()->unsigned();
            $table->integer('commission_agent')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->decimal('exchange_rate', 20, 3)->default(1);
            $table->decimal('total_amount_recovered', 22, 4)->nullable()->comment('Used for stock adjustment.');
            $table->integer('transfer_parent_id')->nullable();
            $table->integer('opening_stock_product_id')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('expense_for')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
        });

        // Copy data from old table and update exchange_rate where it's 0
        DB::statement('INSERT INTO new_transactions (
            id, business_id, type, status, payment_status, adjustment_type, contact_id, invoice_no, ref_no,
            transaction_date, total_before_tax, tax_id, tax_amount, discount_type, discount_amount,
            shipping_details, shipping_charges, additional_notes, staff_note, final_total,
            expense_category_id, expense_for, commission_agent, created_by, location_id, exchange_rate,
            total_amount_recovered, transfer_parent_id, opening_stock_product_id, created_at, updated_at
        ) SELECT 
            id, business_id, type, status, payment_status, adjustment_type, contact_id, invoice_no, ref_no,
            transaction_date, total_before_tax, tax_id, tax_amount, discount_type, discount_amount,
            shipping_details, shipping_charges, additional_notes, staff_note, final_total,
            expense_category_id, expense_for, commission_agent, created_by, location_id, exchange_rate,
            total_amount_recovered, transfer_parent_id, opening_stock_product_id, created_at, updated_at
        FROM transactions');

        DB::statement('UPDATE new_transactions SET exchange_rate = 1 WHERE exchange_rate = 0');

        // Drop old table and rename new table
        Schema::dropIfExists('transactions');
        Schema::rename('new_transactions', 'transactions');
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
