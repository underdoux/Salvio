<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'original_price',
        'adjusted_price',
        'adjustment_reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            if ($orderItem->adjusted_price !== null) {
                $maxDiscount = Setting::get('max_discount_percentage', 20);
                $minAllowedPrice = $orderItem->original_price * (1 - ($maxDiscount / 100));

                if ($orderItem->adjusted_price < $minAllowedPrice) {
                    throw ValidationException::withMessages([
                        'adjusted_price' => [
                            "Price adjustment exceeds maximum allowed discount of {$maxDiscount}%"
                        ]
                    ]);
                }

                if (empty($orderItem->adjustment_reason)) {
                    throw ValidationException::withMessages([
                        'adjustment_reason' => ['Reason is required when adjusting price']
                    ]);
                }
            }
        });

        static::saved(function ($orderItem) {
            $orderItem->order->updateTotal();
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->adjusted_price === null) {
            return null;
        }

        return round((1 - ($this->adjusted_price / $this->original_price)) * 100, 2);
    }
}
