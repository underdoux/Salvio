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
        Schema::create('account_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_account_type_id')->nullable();
            $table->integer('business_id');
            $table->timestamps();
        });

        // For SQLite, we need to recreate the accounts table to modify columns
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create temporary table
            DB::statement('CREATE TEMPORARY TABLE accounts_backup AS SELECT * FROM accounts');
            
            // Drop the original table
            Schema::drop('accounts');
            
            // Recreate accounts table with new schema
            Schema::create('accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('business_id');
                $table->integer('account_type_id')->nullable();
                $table->string('account_number')->nullable();
                $table->text('note')->nullable();
                $table->integer('created_by')->unsigned();
                $table->boolean('is_closed')->default(0);
                $table->timestamps();
            });

            // Get the list of columns from the backup table
            $columns = DB::select("PRAGMA table_info(accounts_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);

            // Remove 'account_type' and 'deleted_at' from the list if they exist
            $column_names = array_filter($column_names, function($col) {
                return !in_array($col, ['account_type', 'deleted_at']);
            });

            // Build the column list string
            $column_list = implode(', ', $column_names);

            // Restore the data
            DB::statement("INSERT INTO accounts ($column_list) SELECT $column_list FROM accounts_backup");
            
            // Drop the temporary table
            DB::statement('DROP TABLE accounts_backup');
        } else {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('account_type');
                $table->integer('account_type_id')->nullable()->after('business_id');
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
        Schema::dropIfExists('account_types');

        Schema::table('accounts', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('business_id');
            $table->dropColumn('account_type_id');
        });
    }
};
