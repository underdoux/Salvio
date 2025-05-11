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
        // Create new business table with updated schema
        Schema::create('new_business', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('currency_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->string('tax_number_1')->nullable();
            $table->string('tax_label_1')->nullable();
            $table->string('tax_number_2')->nullable();
            $table->string('tax_label_2')->nullable();
            $table->float('default_profit_percent', 5, 2)->default(0);
            $table->integer('owner_id')->unsigned();
            $table->string('time_zone')->default('Asia/Kolkata');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->string('accounting_method')->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable();
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('includes');
            $table->string('logo')->nullable();
            $table->string('sku_prefix')->nullable();
            $table->boolean('enable_product_expiry')->default(0);
            $table->string('expiry_type')->default('add_expiry')->nullable();
            $table->string('on_product_expiry')->check("on_product_expiry IN ('keep_selling', 'stop_selling', 'auto_delete')")->default('keep_selling');
            $table->integer('stop_selling_before')->default(0)->comment('Stop selling expied item n days before expiry');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        // Copy data from old table with explicit columns
        DB::statement('INSERT INTO new_business (
            id, name, currency_id, start_date, tax_number_1, tax_label_1, tax_number_2, tax_label_2,
            default_profit_percent, owner_id, time_zone, fy_start_month, accounting_method,
            default_sales_discount, sell_price_tax, logo, sku_prefix, enable_product_expiry,
            expiry_type, created_at, updated_at
        ) SELECT 
            id, name, currency_id, start_date, tax_number_1, tax_label_1, tax_number_2, tax_label_2,
            default_profit_percent, owner_id, time_zone, fy_start_month, accounting_method,
            default_sales_discount, sell_price_tax, logo, sku_prefix, enable_product_expiry,
            expiry_type, created_at, updated_at
        FROM business');

        // Initialize new columns with default values
        DB::statement("UPDATE new_business SET on_product_expiry = 'keep_selling', stop_selling_before = 0");

        // Drop old table and rename new table
        Schema::dropIfExists('business');
        Schema::rename('new_business', 'business');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('on_product_expiry');
            $table->dropColumn('stop_selling_before');
        });
    }
};
