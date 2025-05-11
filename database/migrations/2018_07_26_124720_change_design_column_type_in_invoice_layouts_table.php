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
        // Create new table with desired schema
        Schema::create('invoice_layouts_new', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('header_text')->nullable();
            $table->string('invoice_no_prefix')->nullable();
            $table->string('invoice_heading')->nullable();
            $table->string('sub_total_label')->nullable();
            $table->string('discount_label')->nullable();
            $table->string('tax_label')->nullable();
            $table->string('total_label')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('show_logo')->default(0);
            $table->boolean('show_business_name')->default(0);
            $table->boolean('show_location_name')->default(1);
            $table->boolean('show_landmark')->default(1);
            $table->boolean('show_city')->default(1);
            $table->boolean('show_state')->default(1);
            $table->boolean('show_zip_code')->default(1);
            $table->boolean('show_country')->default(1);
            $table->boolean('show_mobile_number')->default(1);
            $table->boolean('show_alternate_number')->default(0);
            $table->boolean('show_email')->default(0);
            $table->boolean('show_tax_1')->default(1);
            $table->boolean('show_tax_2')->default(0);
            $table->boolean('show_barcode')->default(0);
            $table->string('highlight_color', 10)->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('is_default')->default(0);
            $table->integer('business_id')->unsigned();
            $table->timestamps();
            $table->string('invoice_heading_paid')->nullable();
            $table->string('invoice_heading_not_paid')->nullable();
            $table->string('total_due_label')->nullable();
            $table->string('paid_label')->nullable();
            $table->boolean('show_payments')->default(0);
            $table->boolean('show_customer')->default(0);
            $table->string('customer_label')->nullable();
            $table->string('sub_heading_line1')->nullable();
            $table->string('sub_heading_line2')->nullable();
            $table->string('sub_heading_line3')->nullable();
            $table->string('sub_heading_line4')->nullable();
            $table->string('sub_heading_line5')->nullable();
            $table->string('table_product_label')->nullable();
            $table->string('table_qty_label')->nullable();
            $table->string('table_unit_price_label')->nullable();
            $table->string('table_subtotal_label')->nullable();
            $table->boolean('show_client_id')->default(0);
            $table->string('client_id_label')->nullable();
            $table->string('date_label')->nullable();
            $table->boolean('show_time')->default(1);
            $table->boolean('show_brand')->default(0);
            $table->boolean('show_sku')->default(1);
            $table->boolean('show_cat_code')->default(1);
            $table->boolean('show_sale_description')->default(0);
            $table->text('module_info')->nullable();
            $table->string('quotation_heading')->nullable();
            $table->string('quotation_no_prefix')->nullable();
            $table->string('design', 190)->default('classic');
            $table->string('client_tax_label')->nullable();
            $table->string('cat_code_label')->nullable();
            $table->string('cn_heading')->nullable();
            $table->string('cn_no_label')->nullable();
            $table->string('cn_amount_label')->nullable();
            $table->string('sales_person_label')->nullable();
            $table->boolean('show_sales_person')->default(0);
            $table->boolean('show_expiry')->default(0);
            $table->boolean('show_lot')->default(0);

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // Copy data
        DB::statement('INSERT INTO invoice_layouts_new SELECT * FROM invoice_layouts');

        // Drop old table
        Schema::drop('invoice_layouts');

        // Rename new table
        Schema::rename('invoice_layouts_new', 'invoice_layouts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create old table with enum type
        Schema::create('invoice_layouts_old', function (Blueprint $table) {
            $table->increments('id');
            // ... same columns as up() but with enum type for design
            $table->enum('design', ['classic', 'elegant'])->default('classic');
        });

        // Copy data
        DB::statement('INSERT INTO invoice_layouts_old SELECT * FROM invoice_layouts');

        // Drop new table
        Schema::drop('invoice_layouts');

        // Rename old table
        Schema::rename('invoice_layouts_old', 'invoice_layouts');
    }
};
