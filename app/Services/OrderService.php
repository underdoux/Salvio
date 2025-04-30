<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderEventNotification;

class OrderService
{
    public function getAllOrders()
    {
        return Order::with(['items.product'])->latest()->paginate(10);
    }

    public function createOrder(array $orderData, array $itemsData)
    {
        return DB::transaction(function () use ($orderData, $itemsData) {
            // Get max discount from settings
            $maxDiscount = Setting::get('max_discount_percentage', 20);
            
            // Set default tax if not provided
            if (!isset($orderData['tax'])) {
                $orderData['tax'] = Setting::get('tax_percentage', 10);
            }

            $order = Order::create($orderData);

            foreach ($itemsData as $itemData) {
                $originalPrice = $itemData['original_price'];
                $adjustedPrice = $itemData['adjusted_price'] ?? $originalPrice;
                
                if ($adjustedPrice < $originalPrice) {
                    $discount = (($originalPrice - $adjustedPrice) / $originalPrice) * 100;

                    if ($discount > $maxDiscount) {
                        throw new \Exception("Discount of {$discount}% exceeds maximum allowed discount of {$maxDiscount}%");
                    }

                    if (empty($itemData['adjustment_reason'])) {
                        throw new \Exception('Adjustment reason is required for discounted items');
                    }
                }

                $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'original_price' => $originalPrice,
                    'adjusted_price' => $adjustedPrice,
                    'adjustment_reason' => $itemData['adjustment_reason'] ?? null,
                ]);
            }

            // Update order total
            $order->updateTotal();

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        if (!in_array($status, Order::VALID_STATUSES)) {
            throw new \Exception('Invalid order status');
        }

        $order->update(['status' => $status]);

        return $order;
    }

    public function getOrderDetails(Order $order)
    {
        return $order->load(['items.product']);
    }
}
