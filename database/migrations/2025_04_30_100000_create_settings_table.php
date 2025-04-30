<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'tax_percentage',
                'value' => '10',
                'description' => 'Default tax percentage for orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_discount_percentage',
                'value' => '20',
                'description' => 'Maximum allowed discount percentage for order items',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
