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
        DB::statement('CREATE TEMPORARY TABLE transaction_payments_backup AS SELECT * FROM transaction_payments');
        
        // Drop the original table
        Schema::drop('transaction_payments');
        
        // Recreate the table with the updated column type
        Schema::create('transaction_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('method');
            $table->string('card_transaction_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type', 191)->nullable(); // Changed from enum to varchar
            $table->string('card_holder_name')->nullable();
            $table->string('card_month')->nullable();
            $table->string('card_year')->nullable();
            $table->string('card_security', 5)->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // Restore the data with explicit column list
        DB::statement('INSERT INTO transaction_payments (id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, note, created_at, updated_at) 
            SELECT id, transaction_id, amount, method, card_transaction_number, card_number, card_type, card_holder_name, card_month, card_year, card_security, cheque_number, bank_account_number, note, created_at, updated_at FROM transaction_payments_backup');
        
        // Drop the temporary table
        DB::statement('DROP TABLE transaction_payments_backup');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not needed as we're just changing a column type
    }
};
