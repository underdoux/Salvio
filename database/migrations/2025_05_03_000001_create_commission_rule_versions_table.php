<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commission_rule_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_rule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->json('conditions')->nullable();
            $table->boolean('active')->default(true);
            $table->string('change_reason')->nullable();
            $table->integer('version_number');
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->timestamps();

            // Ensure version numbers are unique per rule
            $table->unique(['commission_rule_id', 'version_number']);
        });

        // Add version tracking columns to commission_rules
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->integer('current_version')->default(1)->after('active');
            $table->timestamp('effective_from')->nullable()->after('current_version');
            $table->timestamp('effective_until')->nullable()->after('effective_from');
            $table->boolean('is_template')->default(false)->after('effective_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_rule_versions');

        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn(['current_version', 'effective_from', 'effective_until', 'is_template']);
        });
    }
};
