<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';

    protected $fillable = [
        'order_item_id',
        'user_id',
        'amount',
        'status',
        'product_id',
        'category_id',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function approve()
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'notes' => $notes
        ]);
    }

    public function markAsPaid()
    {
        $this->update(['status' => self::STATUS_PAID]);
    }
}
