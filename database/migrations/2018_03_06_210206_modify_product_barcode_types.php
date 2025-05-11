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
        // Create new products table with updated schema
        Schema::create('new_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('business_id')->unsigned();
            $table->string('type')->nullable();
            $table->integer('unit_id')->unsigned();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('sub_category_id')->unsigned()->nullable();
            $table->integer('tax')->unsigned()->nullable();
            $table->string('sku');
            $table->string('barcode_type')->check("barcode_type IN ('C39', 'C128', 'EAN13', 'EAN8', 'UPCA', 'UPCE')")->default('C128');
            $table->integer('alert_quantity')->default(0);
            $table->integer('created_by')->unsigned();
            $table->boolean('enable_stock')->default(0);
            $table->integer('expiry_period')->nullable();
            $table->string('expiry_period_type')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Copy data from old table
        DB::statement('INSERT INTO new_products (
            id, name, business_id, type, unit_id, brand_id, category_id, sub_category_id,
            tax, sku, barcode_type, alert_quantity, created_by, enable_stock,
            expiry_period, expiry_period_type, created_at, updated_at
        ) SELECT 
            id, name, business_id, type, unit_id, brand_id, category_id, sub_category_id,
            tax, sku, barcode_type, alert_quantity, created_by, enable_stock,
            expiry_period, expiry_period_type, created_at, updated_at
        FROM products');

        // Drop old table and rename new table
        Schema::dropIfExists('products');
        Schema::rename('new_products', 'products');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
