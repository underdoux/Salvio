<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionRule extends Model
{
    use HasFactory;

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
        'rate' => 'float',
        'min_amount' => 'float',
        'max_amount' => 'float',
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'reference_id')
            ->when($this->type === self::TYPE_PRODUCT);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'reference_id')
            ->when($this->type === self::TYPE_CATEGORY);
    }

    public static function getApplicableRule($product, $amount)
    {
        // First check for product-specific rule
        $rule = self::where('type', self::TYPE_PRODUCT)
            ->where('reference_id', $product->id)
            ->where('is_active', true)
            ->first();

        if ($rule) {
            return $rule;
        }

        // Then check for category rule
        $rule = self::where('type', self::TYPE_CATEGORY)
            ->where('reference_id', $product->category_id)
            ->where('is_active', true)
            ->first();

        if ($rule) {
            return $rule;
        }

        // Finally, return default global rule
        return self::where('type', self::TYPE_GLOBAL)
            ->whereNull('reference_id')
            ->where('is_active', true)
            ->first();
    }
}
