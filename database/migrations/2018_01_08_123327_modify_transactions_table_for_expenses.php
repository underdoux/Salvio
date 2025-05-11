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
        Schema::create('new_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type')->check("type IN ('purchase', 'sell', 'expense')");
            $table->string('status')->check("status IN ('received', 'pending', 'ordered', 'draft', 'final')");
            $table->string('payment_status')->check("payment_status IN ('paid', 'due')");
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
            $table->timestamps();

            // Add foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('expense_for')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // Add indexes
            $table->index('business_id');
            $table->index('type');
            $table->index('contact_id');
            $table->index('transaction_date');
            $table->index('created_by');
            $table->index('expense_category_id');
        });

        // Copy data from old table to new table
        DB::statement('INSERT INTO new_transactions SELECT id, business_id, type, status, payment_status, contact_id, invoice_no, ref_no, transaction_date, total_before_tax, tax_id, tax_amount, discount_type, discount_amount, shipping_details, shipping_charges, additional_notes, staff_note, final_total, NULL as expense_category_id, NULL as expense_for, created_by, location_id, created_at, updated_at FROM transactions');

        // Drop old table and rename new table
        Schema::drop('transactions');
        Schema::rename('new_transactions', 'transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
