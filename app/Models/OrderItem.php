<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'original_price',
        'adjusted_price',
        'adjustment_reason'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'adjusted_price' => 'decimal:2'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->adjusted_price || $this->adjusted_price >= $this->original_price) {
            return null;
        }

        return (($this->original_price - $this->adjusted_price) / $this->original_price) * 100;
    }

    public function getDiscountAmountAttribute(): float
    {
        return $this->original_price - ($this->adjusted_price ?? $this->original_price);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->adjusted_price ?? $this->original_price;
    }

    public function hasDiscount(): bool
    {
        return $this->adjusted_price && $this->adjusted_price < $this->original_price;
    }

    public function validateDiscount(): bool
    {
        if (!$this->hasDiscount()) {
            return true;
        }

        $maxDiscount = Setting::get('max_discount_percentage', 20);
        return $this->discount_percentage <= $maxDiscount;
    }
}
