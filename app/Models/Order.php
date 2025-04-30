<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAID = 'paid';

    const VALID_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_SHIPPED,
        self::STATUS_COMPLETED,
        self::STATUS_PAID,
    ];

    protected $fillable = [
        'tax',
        'status',
        'total',
        'payment_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!isset($order->tax)) {
                $order->tax = Setting::get('tax_percentage', 10);
            }
            if (!isset($order->status)) {
                $order->status = self::STATUS_NEW;
            }
        });

        static::saving(function ($order) {
            if (!in_array($order->status, self::VALID_STATUSES)) {
                throw ValidationException::withMessages([
                    'status' => ['Invalid order status.']
                ]);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->adjusted_price ?? $item->original_price;
        });

        return $subtotal * (1 + ($this->tax / 100));
    }

    public function updateTotal(): void
    {
        $this->total = $this->calculateTotal();
        $this->save();
    }
}
