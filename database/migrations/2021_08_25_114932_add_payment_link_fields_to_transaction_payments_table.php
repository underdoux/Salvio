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
            DB::statement('CREATE TEMPORARY TABLE transaction_payments_backup AS SELECT * FROM transaction_payments');
            Schema::drop('transaction_payments');

            Schema::create('transaction_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->nullable();
                $table->decimal('amount', 22, 4)->default(0);
                $table->enum('method', ['cash', 'card', 'cheque', 'bank_transfer', 'other', 'custom_pay_1', 'custom_pay_2', 'custom_pay_3'])->default('cash');
                $table->string('transaction_no')->nullable();
                $table->string('card_transaction_number')->nullable();
                $table->string('card_number')->nullable();
                $table->enum('card_type', ['visa', 'master', 'other'])->nullable();
                $table->string('card_holder_name')->nullable();
                $table->string('card_month')->nullable();
                $table->string('card_year')->nullable();
                $table->string('card_security', 5)->nullable();
                $table->string('cheque_number')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->dateTime('paid_on')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('payment_for')->nullable();
                $table->integer('parent_id')->nullable();
                $table->string('note')->nullable();
                $table->string('document')->nullable();
                $table->boolean('is_return')->default(0);
                $table->integer('account_id')->nullable();
                $table->integer('business_id');
                $table->tinyInteger('is_advance')->default(0);
                $table->string('payment_ref_no')->nullable();
                $table->integer('advance_payment_id')->nullable();
                $table->string('payment_link_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_payments_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'payment_link_id';
            }));

            DB::statement("INSERT INTO transaction_payments ($column_list) SELECT $column_list FROM transaction_payments_backup");
            DB::statement('DROP TABLE transaction_payments_backup');
        } else {
            Schema::table('transaction_payments', function (Blueprint $table) {
                $table->string('payment_link_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropColumn('payment_link_id');
        });
    }
};
