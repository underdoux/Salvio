<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('bpom_code')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('reorder_point')->default(10);
            $table->boolean('is_by_order')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index for faster searches
            $table->index('name');
            $table->index('bpom_code');
            $table->index(['stock', 'is_by_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
