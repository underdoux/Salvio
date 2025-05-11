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
        DB::statement('CREATE TEMPORARY TABLE transactions_backup AS SELECT * FROM transactions');
        
        // Drop the original table
        Schema::drop('transactions');
        
        // Recreate the table with the updated structure
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type')->nullable(); // Changed to allow purchase_return
            $table->string('status');
            $table->string('payment_status');
            $table->integer('contact_id')->unsigned();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0);
            $table->integer('tax_id')->unsigned()->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('created_by')->unsigned();
            $table->integer('return_parent_id')->nullable();
            $table->timestamps();
        });

        // Restore the data with explicit column list
        DB::statement('INSERT INTO transactions (id, business_id, type, status, payment_status, contact_id, invoice_no, ref_no, transaction_date, total_before_tax, tax_id, tax_amount, discount_type, discount_amount, shipping_details, shipping_charges, additional_notes, staff_note, final_total, created_by, created_at, updated_at, return_parent_id) 
            SELECT id, business_id, type, status, payment_status, contact_id, invoice_no, ref_no, transaction_date, total_before_tax, tax_id, tax_amount, discount_type, discount_amount, shipping_details, shipping_charges, additional_notes, staff_note, final_total, created_by, created_at, updated_at, NULL FROM transactions_backup');
        
        // Drop the temporary table
        DB::statement('DROP TABLE transactions_backup');
        
        // Add indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('type');
            $table->index('contact_id');
            $table->index('transaction_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('return_parent_id');
            $table->string('type')->change();
        });
    }
};
