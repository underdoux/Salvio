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
        Schema::create('commission_rule_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_rule_id')->constrained()->onDelete('cascade');
            $table->foreignId('depends_on_rule_id')->constrained('commission_rules')->onDelete('cascade');
            $table->enum('dependency_type', ['requires', 'conflicts', 'overrides']);
            $table->text('reason')->nullable();
            $table->timestamps();

            // Ensure no duplicate dependencies
            $table->unique(['commission_rule_id', 'depends_on_rule_id', 'dependency_type']);
        });

        Schema::create('commission_rule_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_a_id')->constrained('commission_rules')->onDelete('cascade');
            $table->foreignId('rule_b_id')->constrained('commission_rules')->onDelete('cascade');
            $table->enum('conflict_type', ['condition_overlap', 'value_conflict', 'date_overlap']);
            $table->json('conflict_details');
            $table->boolean('resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Ensure no duplicate conflicts
            $table->unique(['rule_a_id', 'rule_b_id', 'conflict_type']);
        });

        Schema::table('commission_rules', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false)->after('is_template');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('requires_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->integer('priority')->default(0)->after('approved_at');
            $table->json('metadata')->nullable()->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_rule_conflicts');
        Schema::dropIfExists('commission_rule_dependencies');

        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'requires_approval',
                'approved_by',
                'approved_at',
                'priority',
                'metadata'
            ]);
        });
    }
};
