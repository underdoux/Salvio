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
            DB::statement('CREATE TEMPORARY TABLE contacts_backup AS SELECT * FROM contacts');
            Schema::drop('contacts');

            Schema::create('contacts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('business_id')->unsigned();
                $table->string('type')->index();
                $table->string('supplier_business_name')->nullable();
                $table->string('name')->nullable();
                $table->string('prefix')->nullable();
                $table->string('first_name')->nullable();
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->text('address_line_1')->nullable();
                $table->text('address_line_2')->nullable();
                $table->string('zip_code')->nullable();
                $table->date('dob')->nullable();
                $table->string('contact_id')->nullable();
                $table->string('tax_number')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('mobile')->nullable();
                $table->string('landline')->nullable();
                $table->string('alternate_number')->nullable();
                $table->integer('pay_term_number')->nullable();
                $table->enum('pay_term_type', ['days', 'months'])->nullable();
                $table->decimal('credit_limit', 22, 4)->nullable();
                $table->integer('created_by')->unsigned();
                $table->boolean('is_default')->default(0);
                $table->string('email')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('position')->nullable();
                $table->text('custom_field1')->nullable();
                $table->text('custom_field2')->nullable();
                $table->text('custom_field3')->nullable();
                $table->text('custom_field4')->nullable();
                $table->text('custom_field5')->nullable();
                $table->text('custom_field6')->nullable();
                $table->text('custom_field7')->nullable();
                $table->text('custom_field8')->nullable();
                $table->text('custom_field9')->nullable();
                $table->text('custom_field10')->nullable();
                $table->integer('customer_group_id')->nullable();
                $table->decimal('total_rp', 22, 4)->default(0);
                $table->decimal('total_rp_used', 22, 4)->default(0);
                $table->decimal('total_rp_expired', 22, 4)->default(0);
                $table->string('contact_status')->default('active');
                $table->decimal('balance', 22, 4)->default(0);
                $table->text('shipping_custom_field_details')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });

            // Get columns from backup table
            $columns = DB::select("PRAGMA table_info(contacts_backup)");
            $column_names = array_map(function($col) {
                return $col->name;
            }, $columns);
            
            // Build column list
            $column_list = implode(', ', array_filter($column_names, function($col) {
                return $col != 'shipping_custom_field_details';
            }));

            DB::statement("INSERT INTO contacts ($column_list) SELECT $column_list FROM contacts_backup");
            DB::statement('DROP TABLE contacts_backup');
        } else {
            Schema::table('contacts', function (Blueprint $table) {
                $table->text('shipping_custom_field_details')->nullable();
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
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('shipping_custom_field_details');
        });
    }
};
