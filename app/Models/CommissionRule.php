<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommissionRule extends Model
{
    const TYPE_GLOBAL = 'global';
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';

    protected $fillable = [
        'type',
        'reference_id',
        'rate',
        'min_amount',
        'max_amount',
        'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getApplicableRule($product, $amount)
    {
        // Check product-specific rule
        $rule = self::where('type', self::TYPE_PRODUCT)
            ->where('reference_id', $product->id)
            ->where('is_active', true)
            ->first();

        if ($rule && self::isAmountInRange($amount, $rule)) {
            return $rule;
        }

        // Check category rule
        $rule = self::where('type', self::TYPE_CATEGORY)
            ->where('reference_id', $product->category_id)
            ->where('is_active', true)
            ->first();

        if ($rule && self::isAmountInRange($amount, $rule)) {
            return $rule;
        }

        // Fall back to global rule
        $rule = self::where('type', self::TYPE_GLOBAL)
            ->where('is_active', true)
            ->first();

        if ($rule && self::isAmountInRange($amount, $rule)) {
            return $rule;
        }

        return null;
    }

    private static function isAmountInRange($amount, $rule): bool
    {
        if ($rule->min_amount && $amount < $rule->min_amount) {
            return false;
        }

        if ($rule->max_amount && $amount > $rule->max_amount) {
            return false;
        }

        return true;
    }
}
