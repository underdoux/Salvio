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
            // For transactions table
            DB::statement('CREATE TEMPORARY TABLE transactions_backup AS SELECT * FROM transactions');
            Schema::drop('transactions');
            
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
                $table->string('subscription_repeat_on')->nullable();
                $table->text('order_addresses')->nullable();
                $table->string('sub_type')->nullable();
                $table->decimal('rp_earned', 22, 4)->default(0);
                $table->decimal('rp_redeemed', 22, 4)->default(0);
                $table->decimal('rp_redeemed_amount', 22, 4)->default(0);
                $table->boolean('is_quotation')->default(0);
                $table->decimal('total', 22, 4)->default(0);
                $table->integer('expense_category_id')->nullable();
                $table->integer('expense_for')->nullable();
                $table->integer('commission_agent')->nullable();
                $table->integer('document')->nullable();
                $table->integer('is_direct_sale')->nullable();
                $table->decimal('exchange_rate', 22, 4)->default(1);
                $table->integer('purchase_order_ids')->nullable();
                $table->integer('sales_order_ids')->nullable();
                $table->text('types_of_service_id')->nullable();
                $table->decimal('packing_charge', 22, 4)->default(0);
                $table->string('packing_charge_type')->nullable();
                $table->integer('service_custom_field_1')->nullable();
                $table->integer('service_custom_field_2')->nullable();
                $table->integer('service_custom_field_3')->nullable();
                $table->integer('service_custom_field_4')->nullable();
                $table->integer('mfg_parent_production_purchase_id')->nullable();
                $table->decimal('mfg_wasted_units')->nullable();
                $table->text('mfg_production_cost')->nullable();
                $table->text('mfg_is_final')->nullable();
                $table->text('is_created_from_api')->nullable();
                $table->decimal('round_off_amount', 22, 4)->default(0);
                $table->text('import_batch')->nullable();
                $table->text('import_time')->nullable();
                $table->text('types_of_service_custom_fields')->nullable();
                $table->text('shipping_custom_fields')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('shipping_status')->nullable();
                $table->string('delivered_to')->nullable();
                $table->text('shipping_docs')->nullable();
                $table->integer('shipping_lines_id')->nullable();
                $table->text('additional_expense_value_1')->nullable();
                $table->text('additional_expense_value_2')->nullable();
                $table->text('additional_expense_value_3')->nullable();
                $table->text('additional_expense_value_4')->nullable();
                $table->text('additional_expense_key_1')->nullable();
                $table->text('additional_expense_key_2')->nullable();
                $table->text('additional_expense_key_3')->nullable();
                $table->text('additional_expense_key_4')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO transactions ($column_list) SELECT $column_list FROM transactions_backup");
            DB::statement('DROP TABLE transactions_backup');
        } else {
            DB::statement('ALTER TABLE transactions MODIFY COLUMN `status` VARCHAR(191) NOT NULL');
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
            // For transactions table
            DB::statement('CREATE TEMPORARY TABLE transactions_backup AS SELECT * FROM transactions');
            Schema::drop('transactions');
            
            Schema::create('transactions', function (Blueprint $table) {
                $table->increments('id');
                // Copy the schema from up() but change status back to enum
                $table->enum('status', ['received', 'pending', 'ordered', 'draft', 'final'])->nullable();
                // ... rest of the columns same as up()
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO transactions ($column_list) SELECT $column_list FROM transactions_backup");
            DB::statement('DROP TABLE transactions_backup');
        } else {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN `status` ENUM('received','pending','ordered','draft','final') NOT NULL");
        }
    }
};
