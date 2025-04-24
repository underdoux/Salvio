<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getAllOrders()
    {
        return Order::with('items')->latest()->get();
    }

    public function createOrder(array $orderData, array $itemsData)
    {
        return DB::transaction(function () use ($orderData, $itemsData) {
            $order = Order::create($orderData);

            foreach ($itemsData as $itemData) {
                $maxDiscount = config('commission.max_discount', 0);
                $originalPrice = $itemData['original_price'];
                $adjustedPrice = $itemData['adjusted_price'];
                $discount = ($originalPrice - $adjustedPrice) / $originalPrice * 100;

                if ($discount > $maxDiscount) {
                    throw new \Exception('Discount exceeds maximum allowed');
                }

                if ($discount > 0 && empty($itemData['adjustment_reason'])) {
                    throw new \Exception('Adjustment reason is required for discounted items');
                }

                $order->items()->create($itemData);
            }

            return $order;
        });
    }
}
