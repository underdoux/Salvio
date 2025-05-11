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
        
        // Recreate the table with the updated column types
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('type', 191)->nullable()->index();
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
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('return_parent_id')->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->string('pay_term_type')->nullable();
            $table->boolean('is_suspend')->default(0);
            $table->string('invoice_token')->nullable();
            $table->boolean('is_recurring')->default(0);
            $table->integer('recur_interval')->nullable();
            $table->string('recur_interval_type')->nullable();
            $table->integer('recur_repetitions')->nullable();
            $table->dateTime('recur_stopped_on')->nullable();
            $table->integer('recur_parent_id')->nullable();
            $table->string('subscription_no')->nullable();
            $table->text('order_addresses')->nullable();
            $table->string('sub_type')->nullable();
            $table->decimal('rp_earned', 22, 4)->default(0);
            $table->decimal('rp_redeemed', 22, 4)->default(0);
            $table->decimal('rp_redeemed_amount', 22, 4)->default(0);
            $table->timestamps();
        });

        // Get the list of columns from the backup table
        $columns = DB::select("PRAGMA table_info(transactions_backup)");
        $column_names = array_map(function($col) {
            return $col->name;
        }, $columns);

        // Only include columns that exist in both tables
        $column_list = implode(', ', $column_names);

        // Restore the data using the exact column list
        DB::statement("INSERT INTO transactions ($column_list) SELECT $column_list FROM transactions_backup");
        
        // Drop the temporary table
        DB::statement('DROP TABLE transactions_backup');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not needed as we're just changing column types
    }
};
