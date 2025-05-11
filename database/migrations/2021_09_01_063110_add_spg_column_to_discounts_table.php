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
            DB::statement('CREATE TEMPORARY TABLE discounts_backup AS SELECT * FROM discounts');
            Schema::drop('discounts');

            Schema::create('discounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('business_id');
                $table->integer('brand_id')->nullable();
                $table->integer('category_id')->nullable();
                $table->integer('location_id')->nullable();
                $table->integer('priority')->nullable();
                $table->decimal('discount_amount', 22, 4);
                $table->enum('discount_type', ['fixed', 'percentage']);
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->boolean('is_active')->default(1);
                $table->boolean('is_applicable_in_spg')->default(0);
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(discounts_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list excluding the new column and any non-existent columns
            $valid_columns = [
                'id', 'name', 'business_id', 'brand_id', 'category_id', 
                'location_id', 'priority', 'discount_amount', 'discount_type',
                'starts_at', 'ends_at', 'is_active', 'created_at', 'updated_at'
            ];
            
            $column_list = implode(', ', array_filter($column_names, function($col) use ($valid_columns) {
                return in_array($col, $valid_columns);
            }));

            // Insert data from backup
            DB::statement("INSERT INTO discounts ($column_list) SELECT $column_list FROM discounts_backup");
            DB::statement('DROP TABLE discounts_backup');
        } else {
            Schema::table('discounts', function (Blueprint $table) {
                $table->boolean('is_applicable_in_spg')->default(0);
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
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn('is_applicable_in_spg');
        });
    }
};
