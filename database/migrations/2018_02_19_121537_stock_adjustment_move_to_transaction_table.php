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
        // Create new transactions table with updated schema
        Schema::create('new_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type')->check("type IN ('purchase', 'sell', 'expense', 'stock_adjustment')");
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
            $table->integer('created_by')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->decimal('exchange_rate', 22, 4)->default(1);
            $table->decimal('total_amount_recovered', 22, 4)->nullable()->comment('Used for stock adjustment.');
            $table->timestamps();

            // Add foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('expense_for')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
        });

        // Copy existing data
        DB::statement('INSERT INTO new_transactions (
            id, business_id, type, status, payment_status, contact_id, invoice_no, ref_no, 
            transaction_date, total_before_tax, tax_id, tax_amount, discount_type, 
            discount_amount, shipping_details, shipping_charges, additional_notes, 
            staff_note, final_total, expense_category_id, expense_for, created_by, 
            location_id, exchange_rate, created_at, updated_at
        ) SELECT 
            id, business_id, type, status, payment_status, contact_id, invoice_no, ref_no, 
            transaction_date, total_before_tax, tax_id, tax_amount, discount_type, 
            discount_amount, shipping_details, shipping_charges, additional_notes, 
            staff_note, final_total, expense_category_id, expense_for, created_by, 
            location_id, exchange_rate, created_at, updated_at 
        FROM transactions');

        // Initialize new columns with default values
        DB::statement('UPDATE new_transactions SET adjustment_type = NULL, total_amount_recovered = NULL');

        // Drop old table and rename new table
        Schema::dropIfExists('transactions');
        Schema::rename('new_transactions', 'transactions');

        // Create stock adjustment lines table
        Schema::create('stock_adjustment_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('variation_id')->unsigned();
            $table->foreign('variation_id')->references('id')->on('variations')
            ->onDelete('cascade');
            $table->decimal('quantity', 22, 4);
            $table->decimal('unit_price', 22, 4)->comment('Last purchase unit price')->nullable();
            $table->timestamps();

            //Indexing
            $table->index('transaction_id');
        });

        //Create & Rename stock_adjustment table.
        DB::statement('CREATE TABLE IF NOT EXISTS `stock_adjustments` (`id` int(11) DEFAULT NULL) ');
        Schema::rename('stock_adjustments', 'stock_adjustments_temp');
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
