<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderEventNotification;

class OrderService
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

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

                    if ($discount > $maxDiscount && !Auth::user()->hasRole('Admin')) {
                        throw new \Exception("Discount of {$discount}% exceeds maximum allowed discount of {$maxDiscount}%");
                    }

                    if (empty($itemData['adjustment_reason'])) {
                        throw new \Exception('Adjustment reason is required for discounted items');
                    }
                }

                $orderItem = $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'original_price' => $originalPrice,
                    'adjusted_price' => $adjustedPrice,
                    'adjustment_reason' => $itemData['adjustment_reason'] ?? null,
                ]);

                // Calculate and create commission record
                if (Auth::user()->hasRole(['Sales', 'Cashier'])) {
                    $this->commissionService->calculateCommission($orderItem);
                }
            }

            // Update order total
            $order->updateTotal();

            // Send notification
            $order->user->notify(new OrderEventNotification(
                $order,
                'created',
                "Your order #{$order->id} has been created successfully."
            ));

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        if (!in_array($status, Order::VALID_STATUSES)) {
            throw new \Exception('Invalid order status');
        }

        DB::transaction(function () use ($order, $status) {
            $order->update(['status' => $status]);

            // Process commissions when order is completed
            if ($status === Order::STATUS_COMPLETED) {
                foreach ($order->items as $item) {
                    $commission = $item->commission;
                    if ($commission && $commission->status === 'pending') {
                        $commission->approve();
                    }
                }
            }

            // Send notification
            $order->user->notify(new OrderEventNotification(
                $order,
                'status_updated',
                "Your order #{$order->id} status has been updated to {$status}."
            ));
        });

        return $order;
    }

    public function getOrderDetails(Order $order)
    {
        return $order->load(['items.product', 'items.commission']);
    }
}
