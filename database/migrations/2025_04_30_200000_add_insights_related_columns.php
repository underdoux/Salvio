<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add customer_type to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_type')->nullable()->after('status');
        });

        // Add stock and reorder_point to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'reorder_point')) {
                $table->integer('reorder_point')->default(10)->after('stock');
            }
            if (!Schema::hasColumn('products', 'bpom_reference_id')) {
                $table->string('bpom_reference_id')->nullable()->after('category_id');
            }
        });

        // Add category_id to products table if it doesn't exist
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        // Create categories table if it doesn't exist
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Create bpom_references table if it doesn't exist
        if (!Schema::hasTable('bpom_references')) {
            Schema::create('bpom_references', function (Blueprint $table) {
                $table->id();
                $table->string('registration_number')->unique();
                $table->string('product_name');
                $table->string('manufacturer');
                $table->string('category');
                $table->date('registration_date');
                $table->date('expiry_date');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Remove customer_type from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });

        // Remove columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'reorder_point', 'bpom_reference_id']);
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        // Drop bpom_references table
        Schema::dropIfExists('bpom_references');

        // Drop categories table
        Schema::dropIfExists('categories');
    }
};
