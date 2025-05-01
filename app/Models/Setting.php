<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function formatMoney($amount)
    {
        $symbol = static::get('currency_symbol', 'Rp');
        $position = static::get('currency_position', 'before');

        // Format the number with thousand separator
        $formattedAmount = number_format($amount, 0, ',', '.');

        return $position === 'before'
            ? "{$symbol} {$formattedAmount}"
            : "{$formattedAmount} {$symbol}";
    }
}
