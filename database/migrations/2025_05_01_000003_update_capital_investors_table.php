<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('capital_investors', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->decimal('investment_amount', 15, 2)->default(0)->after('ownership_percentage');
            $table->date('investment_date')->nullable()->after('investment_amount');
            $table->text('notes')->nullable()->after('investment_date');
            $table->boolean('is_active')->default(true)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('capital_investors', function (Blueprint $table) {
            $table->dropColumn(['email', 'investment_amount', 'investment_date', 'notes', 'is_active']);
        });
    }
};
