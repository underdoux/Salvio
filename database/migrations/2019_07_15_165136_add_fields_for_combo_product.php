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
        Schema::table('variations', function (Blueprint $table) {
            $table->text('combo_variations')->nullable()->comment('Contains the combo variation details');
        });

        // Create a temporary table for products
        DB::statement('CREATE TEMPORARY TABLE products_backup AS SELECT * FROM products');
        
        // Drop the original table
        Schema::drop('products');
        
        // Recreate the table with the updated column type
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('business_id');
            $table->string('type')->nullable(); // Changed to string instead of enum
            $table->integer('unit_id')->unsigned();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('sub_category_id')->unsigned()->nullable();
            $table->integer('tax')->unsigned()->nullable();
            $table->string('tax_type')->nullable();
            $table->integer('enable_stock')->default(0);
            $table->decimal('alert_quantity', 22, 4)->default(0);
            $table->string('sku');
            $table->string('barcode_type')->nullable();
            $table->integer('expiry_period')->nullable();
            $table->string('expiry_period_type')->nullable();
            $table->integer('enable_sr_no')->default(0);
            $table->text('image')->nullable();
            $table->text('product_description')->nullable();
            $table->integer('created_by')->unsigned();
            $table->boolean('is_inactive')->default(0);
            $table->text('custom_field1')->nullable();
            $table->text('custom_field2')->nullable();
            $table->text('custom_field3')->nullable();
            $table->text('custom_field4')->nullable();
            $table->decimal('weight', 22, 4)->nullable();
            $table->timestamps();
        });

        // Get columns from backup table
        $columns = DB::select("PRAGMA table_info(products_backup)");
        $column_map = [];
        foreach ($columns as $col) {
            if (strpos($col->name, 'product_custom_field') === 0) {
                // Map product_custom_field to custom_field
                $new_name = str_replace('product_', '', $col->name);
                $column_map[$col->name] = $new_name;
            } else {
                $column_map[$col->name] = $col->name;
            }
        }

        // Build column lists for INSERT
        $target_columns = implode(', ', array_values($column_map));
        $source_columns = implode(', ', array_keys($column_map));

        // Restore the data using the mapped columns
        DB::statement("INSERT INTO products ($target_columns) SELECT $source_columns FROM products_backup");
        
        // Drop the temporary table
        DB::statement('DROP TABLE products_backup');

        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->string('children_type')
                ->default('')
                ->after('parent_sell_line_id')
                ->comment('Type of children for the parent, like modifier or combo');

            $table->index(['children_type']);
            $table->index(['parent_sell_line_id']);
        });

        DB::statement("UPDATE transaction_sell_lines SET children_type='modifier' WHERE parent_sell_line_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->dropColumn(['combo_variations']);
        });
    }
};
