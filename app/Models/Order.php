<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'tax',
        'status',
        'total',
        'payment_type',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
