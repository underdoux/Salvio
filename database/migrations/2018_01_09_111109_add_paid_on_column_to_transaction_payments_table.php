<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('new_transaction_payments', function (Blueprint $table) {
            // Copy existing columns
            $table->increments('id');
            $table->integer('transaction_id')->unsigned();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('method')->nullable();
            $table->string('card_transaction_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable()->check("card_type IN ('visa', 'master')");
            $table->string('card_holder_name')->nullable();
            $table->string('card_month')->nullable();
            $table->string('card_year')->nullable();
            $table->string('card_security', 5)->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->dateTime('paid_on')->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            // Add foreign keys
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Add indexes
            $table->index('created_by');
        });

        // Copy data from old table
        DB::statement('INSERT INTO new_transaction_payments (id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, created_at, updated_at) SELECT id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, created_at, updated_at FROM transaction_payments');

        // Update the new columns with default values
        DB::statement('UPDATE new_transaction_payments SET paid_on = created_at, created_by = (SELECT id FROM users LIMIT 1)');

        // Drop old table and rename new table
        Schema::dropIfExists('transaction_payments');
        Schema::rename('new_transaction_payments', 'transaction_payments');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            //
        });
    }
};
