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
        Schema::table('contacts', function (Blueprint $table) {
            $table->decimal('balance', 22, 4)->default(0)->after('created_by');
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->default(0)->after('created_by');
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            // For transaction_payments table
            DB::statement('CREATE TEMPORARY TABLE transaction_payments_backup AS SELECT * FROM transaction_payments');
            Schema::drop('transaction_payments');
            
            Schema::create('transaction_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->nullable();
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('method')->nullable();
                $table->string('card_transaction_number')->nullable();
                $table->string('card_number')->nullable();
                $table->enum('card_type', ['credit', 'debit', 'visa', 'master'])->nullable();
                $table->string('card_holder_name')->nullable();
                $table->string('card_month')->nullable();
                $table->string('card_year')->nullable();
                $table->string('card_security', 5)->nullable();
                $table->string('cheque_number')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->dateTime('paid_on')->nullable();
                $table->integer('created_by')->nullable();
                $table->boolean('is_advance')->default(0);
                $table->integer('payment_for')->nullable();
                $table->integer('parent_id')->nullable();
                $table->string('note')->nullable();
                $table->string('document')->nullable();
                $table->integer('payment_ref_no')->nullable();
                $table->integer('account_id')->nullable();
                $table->integer('business_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_payments_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list excluding is_advance
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'is_advance';
            }));

            // Insert data with explicit column names
            DB::statement("INSERT INTO transaction_payments ($column_list) SELECT $column_list FROM transaction_payments_backup");
            DB::statement('DROP TABLE transaction_payments_backup');

            // For cash_register_transactions table
            DB::statement('CREATE TEMPORARY TABLE cash_register_transactions_backup AS SELECT * FROM cash_register_transactions');
            Schema::drop('cash_register_transactions');
            
            Schema::create('cash_register_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('cash_register_id');
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('pay_method')->nullable();
                $table->enum('type', ['debit', 'credit']);
                $table->enum('transaction_type', ['initial', 'sell', 'transfer', 'refund']);
                $table->integer('transaction_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(cash_register_transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO cash_register_transactions ($column_list) SELECT $column_list FROM cash_register_transactions_backup");
            DB::statement('DROP TABLE cash_register_transactions_backup');
        } else {
            DB::statement('ALTER TABLE transaction_payments MODIFY COLUMN `method` VARCHAR(191) DEFAULT NULL');
            DB::statement('ALTER TABLE cash_register_transactions MODIFY COLUMN `pay_method` VARCHAR(191) DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropColumn('is_advance');
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            // For transaction_payments table
            DB::statement('CREATE TEMPORARY TABLE transaction_payments_backup AS SELECT * FROM transaction_payments');
            Schema::drop('transaction_payments');
            
            Schema::create('transaction_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->nullable();
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('method')->nullable();
                $table->string('card_transaction_number')->nullable();
                $table->string('card_number')->nullable();
                $table->enum('card_type', ['credit', 'debit', 'visa', 'master'])->nullable();
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
                $table->integer('payment_ref_no')->nullable();
                $table->integer('account_id')->nullable();
                $table->integer('business_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(transaction_payments_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list excluding is_advance
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'is_advance';
            }));

            // Insert data with explicit column names
            DB::statement("INSERT INTO transaction_payments ($column_list) SELECT $column_list FROM transaction_payments_backup");
            DB::statement('DROP TABLE transaction_payments_backup');

            // For cash_register_transactions table
            DB::statement('CREATE TEMPORARY TABLE cash_register_transactions_backup AS SELECT * FROM cash_register_transactions');
            Schema::drop('cash_register_transactions');
            
            Schema::create('cash_register_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('cash_register_id');
                $table->decimal('amount', 22, 4)->default(0);
                $table->string('pay_method')->nullable();
                $table->enum('type', ['debit', 'credit']);
                $table->enum('transaction_type', ['initial', 'sell', 'transfer', 'refund']);
                $table->integer('transaction_id')->nullable();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(cash_register_transactions_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', $column_names);

            DB::statement("INSERT INTO cash_register_transactions ($column_list) SELECT $column_list FROM cash_register_transactions_backup");
            DB::statement('DROP TABLE cash_register_transactions_backup');
        }
    }
};
