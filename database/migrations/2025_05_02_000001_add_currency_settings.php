<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Insert default currency settings
        DB::table('settings')->insert([
            [
                'key' => 'currency_code',
                'value' => 'IDR',
                'description' => 'Default currency code for the system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Rp',
                'description' => 'Currency symbol used in displaying amounts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_position',
                'value' => 'before',
                'description' => 'Position of currency symbol (before/after)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbols',
                'value' => json_encode([
                    'IDR' => 'Rp',
                    'USD' => '$',
                    'EUR' => '€',
                    'SGD' => 'S$',
                    'MYR' => 'RM',
                    'JPY' => '¥',
                    'CNY' => '¥',
                    'KRW' => '₩'
                ]),
                'description' => 'Available currency symbols',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        DB::table('settings')
            ->whereIn('key', ['currency_code', 'currency_symbol', 'currency_position'])
            ->delete();
    }
};
