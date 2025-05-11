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
        Schema::table('transactions', function (Blueprint $table) {
            // sales_order_ids already exists, so we don't need to add it again
            $table->text('sales_order_return_ids')->nullable();
        });

        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->text('sales_order_line_id')->nullable();
            $table->decimal('so_quantity_invoiced', 22, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('sales_order_return_ids');
        });

        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropColumn('sales_order_line_id');
            $table->dropColumn('so_quantity_invoiced');
        });
    }
};
