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
        Schema::create('transaction_payments_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->nullable();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('method')->default('cash');
            $table->string('card_transaction_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('card_month')->nullable();
            $table->string('card_year')->nullable();
            $table->string('card_security')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->timestamp('paid_on')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('payment_for')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamps();
            $table->boolean('is_return')->default(0);
            $table->string('payment_ref_no')->nullable();
            $table->string('document')->nullable();
            $table->string('note')->nullable();
        });

        // Copy data from old table
        DB::statement('INSERT INTO transaction_payments_new (id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, paid_on, created_by, payment_for, parent_id, created_at, updated_at, is_return, payment_ref_no) SELECT * FROM transaction_payments');

        // Drop old table
        Schema::drop('transaction_payments');

        // Rename new table
        Schema::rename('transaction_payments_new', 'transaction_payments');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create old table
        Schema::create('transaction_payments_old', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->nullable();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('method')->default('cash');
            $table->string('card_transaction_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('card_month')->nullable();
            $table->string('card_year')->nullable();
            $table->string('card_security')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->timestamp('paid_on')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('payment_for')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamps();
            $table->boolean('is_return')->default(0);
            $table->string('payment_ref_no')->nullable();
        });

        // Copy data back
        DB::statement('INSERT INTO transaction_payments_old SELECT id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, paid_on, created_by, payment_for, parent_id, created_at, updated_at, is_return, payment_ref_no FROM transaction_payments');

        // Drop new table
        Schema::drop('transaction_payments');

        // Rename old table back
        Schema::rename('transaction_payments_old', 'transaction_payments');
    }
};
