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
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we'll just update the default value
            DB::statement('UPDATE products SET alert_quantity = NULL WHERE alert_quantity = 0');
        } else {
            DB::statement("ALTER TABLE products MODIFY COLUMN alert_quantity DECIMAL(22,4) DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we'll just update NULL values back to 0
            DB::statement('UPDATE products SET alert_quantity = 0 WHERE alert_quantity IS NULL');
        } else {
            DB::statement("ALTER TABLE products MODIFY COLUMN alert_quantity DECIMAL(22,4) NOT NULL DEFAULT '0'");
        }
    }
};
