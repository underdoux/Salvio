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
                $adjustedPrice = $itemData['adjusted_price'] ?? $originalPrice;
                $discount = ($originalPrice - $adjustedPrice) / $originalPrice * 100;

                if ($discount > $maxDiscount) {
                    throw new \Exception('Discount exceeds maximum allowed');
                }

                if ($discount > 0 && empty($itemData['adjustment_reason'])) {
                    throw new \Exception('Adjustment reason is required for discounted items');
                }

                // Calculate commission
                $commissionService = new CommissionService();
                $commissionAmount = $commissionService->calculateCommission(
                    $itemData['product_id'],
                    $itemData['category_id'] ?? null,
                    $originalPrice
                );

                $itemData['commission_rate'] = $commissionAmount > 0 ? ($commissionAmount / $originalPrice) * 100 : 0;
                $itemData['commission_amount'] = $commissionAmount;

                $order->items()->create($itemData);
            }

            return $order;
        });
    }
}
