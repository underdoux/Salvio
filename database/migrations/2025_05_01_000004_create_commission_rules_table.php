<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'global', 'category', 'product'
            $table->unsignedBigInteger('reference_id')->nullable(); // product_id or category_id
            $table->decimal('rate', 5, 2); // percentage
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('commission_rules');
    }
};
