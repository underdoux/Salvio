<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get total sales for this category
     */
    public function getTotalSalesAttribute(): float
    {
        return $this->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->sum('order_items.original_price');
    }

    /**
     * Get total orders for this category
     */
    public function getTotalOrdersAttribute(): int
    {
        return $this->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->distinct()
            ->count('orders.id');
    }

    /**
     * Get low stock products in this category
     */
    public function getLowStockProductsAttribute()
    {
        return $this->products()
            ->where('stock', '<=', \DB::raw('reorder_point'))
            ->get();
    }
}
