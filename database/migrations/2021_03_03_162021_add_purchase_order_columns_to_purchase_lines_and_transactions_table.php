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
            DB::statement('CREATE TEMPORARY TABLE transactions_backup AS SELECT * FROM transactions');
            Schema::drop('transactions');

            Schema::create('transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('business_id')->unsigned();
                $table->string('type');
                $table->string('status')->nullable();
                $table->string('payment_status')->nullable();
                $table->integer('contact_id')->unsigned()->nullable();
                $table->string('invoice_no')->nullable();
                $table->string('ref_no')->nullable();
                $table->dateTime('transaction_date');
                $table->decimal('total_before_tax', 22, 4)->default(0);
                $table->integer('tax_id')->nullable();
                $table->decimal('tax_amount', 22, 4)->default(0);
                $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
                $table->decimal('discount_amount', 22, 4)->default(0);
                $table->text('shipping_details')->nullable();
                $table->decimal('shipping_charges', 22, 4)->default(0);
                $table->text('additional_notes')->nullable();
                $table->text('staff_note')->nullable();
                $table->decimal('final_total', 22, 4)->default(0);
                $table->integer('created_by')->unsigned();
                $table->integer('location_id')->unsigned()->nullable();
                $table->integer('return_parent_id')->nullable();
                $table->integer('pay_term_number')->nullable();
                $table->enum('pay_term_type', ['days', 'months'])->nullable();
                $table->boolean('is_suspend')->default(0);
                $table->string('invoice_token')->nullable();
                $table->boolean('is_recurring')->default(0);
                $table->integer('recur_interval')->nullable();
                $table->enum('recur_interval_type', ['days', 'months', 'years'])->nullable();
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
                $table->string('document')->nullable();
                $table->boolean('is_direct_sale')->default(0);
                $table->decimal('exchange_rate', 22, 4)->default(1);
                $table->string('sales_order_ids')->nullable();
                $table->integer('types_of_service_id')->nullable();
                $table->decimal('packing_charge', 22, 4)->default(0);
                $table->enum('packing_charge_type', ['fixed', 'percent'])->nullable();
                $table->string('service_custom_field_1')->nullable();
                $table->string('service_custom_field_2')->nullable();
                $table->string('service_custom_field_3')->nullable();
                $table->string('service_custom_field_4')->nullable();
                $table->integer('mfg_parent_production_purchase_id')->nullable();
                $table->decimal('mfg_wasted_units', 22, 4)->nullable();
                $table->decimal('mfg_production_cost', 22, 4)->default(0);
                $table->boolean('mfg_is_final')->default(0);
                $table->boolean('is_created_from_api')->default(0);
                $table->decimal('round_off_amount', 22, 4)->default(0);
                $table->string('import_batch')->nullable();
                $table->string('import_time')->nullable();
                $table->text('types_of_service_custom_fields')->nullable();
                $table->text('shipping_custom_fields')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('shipping_status')->nullable();
                $table->string('delivered_to')->nullable();
                $table->string('shipping_docs')->nullable();
                $table->string('shipping_lines_id')->nullable();
                $table->decimal('additional_expense_value_1', 22, 4)->default(0);
                $table->decimal('additional_expense_value_2', 22, 4)->default(0);
                $table->decimal('additional_expense_value_3', 22, 4)->default(0);
                $table->decimal('additional_expense_value_4', 22, 4)->default(0);
                $table->string('additional_expense_key_1')->nullable();
                $table->string('additional_expense_key_2')->nullable();
                $table->string('additional_expense_key_3')->nullable();
                $table->string('additional_expense_key_4')->nullable();
                $table->string('shipping_custom_field_1')->nullable();
                $table->string('shipping_custom_field_2')->nullable();
                $table->string('shipping_custom_field_3')->nullable();
                $table->string('shipping_custom_field_4')->nullable();
                $table->string('shipping_custom_field_5')->nullable();
                $table->string('sub_status')->nullable();
                $table->string('custom_field_1')->nullable();
                $table->string('custom_field_2')->nullable();
                $table->string('custom_field_3')->nullable();
                $table->string('custom_field_4')->nullable();
                $table->text('purchase_order_ids')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'purchase_order_ids';
            }));

            DB::statement("INSERT INTO transactions ($column_list) SELECT $column_list FROM transactions_backup");
            DB::statement('DROP TABLE transactions_backup');
        } else {
            Schema::table('transactions', function (Blueprint $table) {
                $table->text('purchase_order_ids')->nullable();
            });
        }

        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->text('purchase_order_line_id')->nullable();
            $table->decimal('purchase_order_quantity', 22, 4)->nullable();
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
            $table->dropColumn('purchase_order_ids');
        });

        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->dropColumn('purchase_order_line_id');
            $table->dropColumn('purchase_order_quantity');
        });
    }
};
