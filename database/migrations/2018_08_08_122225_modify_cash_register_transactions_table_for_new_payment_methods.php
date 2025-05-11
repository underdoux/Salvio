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
        Schema::create('cash_register_transactions_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cash_register_id')->unsigned();
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('pay_method')->default('cash');
            $table->string('type');
            $table->string('transaction_type');
            $table->integer('transaction_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_register_id')
                ->references('id')->on('cash_registers')
                ->onDelete('cascade');
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');
        });

        // Copy data from old table
        DB::statement('INSERT INTO cash_register_transactions_new SELECT * FROM cash_register_transactions');

        // Drop old table
        Schema::drop('cash_register_transactions');

        // Rename new table
        Schema::rename('cash_register_transactions_new', 'cash_register_transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create old table
        Schema::create('cash_register_transactions_old', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cash_register_id')->unsigned();
            $table->decimal('amount', 22, 4)->default(0);
            $table->enum('pay_method', ['cash', 'card', 'cheque', 'bank_transfer'])->default('cash');
            $table->string('type');
            $table->string('transaction_type');
            $table->integer('transaction_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_register_id')
                ->references('id')->on('cash_registers')
                ->onDelete('cascade');
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');
        });

        // Copy data back
        DB::statement('INSERT INTO cash_register_transactions_old SELECT * FROM cash_register_transactions');

        // Drop new table
        Schema::drop('cash_register_transactions');

        // Rename old table back
        Schema::rename('cash_register_transactions_old', 'cash_register_transactions');
    }
};
