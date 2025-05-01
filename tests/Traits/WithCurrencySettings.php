<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

trait WithCurrencySettings
{
    protected function setUpCurrencySettings()
    {
        // Drop existing tables if they exist
        if (Schema::hasTable('commission_rules')) {
            DB::statement('DROP TABLE IF EXISTS commission_rules');
        }

        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('settings');

        // Create settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        // Create model_has_roles table
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type']);

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });

        // Create commission_rules table only if it doesn't exist
        if (!Schema::hasTable('commission_rules')) {
            Schema::create('commission_rules', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->decimal('rate', 5, 2);
                $table->decimal('min_amount', 10, 2)->nullable();
                $table->decimal('max_amount', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create the Admin role if it doesn't exist
        if (!\Spatie\Permission\Models\Role::where('name', 'Admin')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        }
    }
}
