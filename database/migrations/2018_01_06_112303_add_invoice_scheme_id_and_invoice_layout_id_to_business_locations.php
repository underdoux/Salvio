<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('business_locations', function (Blueprint $table) {
            $table->integer('invoice_scheme_id')->unsigned()->nullable()->after('zip_code');
            $table->integer('invoice_layout_id')->unsigned()->nullable()->after('invoice_scheme_id');
            
            // Add foreign key constraints after creating the columns
            $table->foreign('invoice_scheme_id')->references('id')->on('invoice_schemes')->onDelete('cascade');
            $table->foreign('invoice_layout_id')->references('id')->on('invoice_layouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_locations', function (Blueprint $table) {
            //
        });
    }
};
