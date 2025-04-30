<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add commission_rate to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('name');
        });

        // Add commission_rate to products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('price');
        });

        // Create commission_rules table
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // global, category, product
            $table->unsignedBigInteger('reference_id')->nullable(); // category_id or product_id
            $table->decimal('rate', 5, 2);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'reference_id']);
        });

        // Add operational_cost to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('operational_cost', 10, 2)->default(0)->after('tax');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });

        Schema::dropIfExists('commission_rules');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('operational_cost');
        });
    }
};
