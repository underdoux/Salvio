<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionRulePreviewController extends Controller
{
    /**
     * Show the preview form for the specified commission rule.
     */
    public function show(CommissionRule $commissionRule): View
    {
        return view('commission-rules.preview', compact('commissionRule'));
    }

    /**
     * Calculate commission based on sample data.
     */
    public function calculate(Request $request, CommissionRule $commissionRule)
    {
        $validated = $request->validate([
            'order_value' => ['required', 'numeric', 'min:0'],
            'product_category' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = [
            'commission_amount' => 0,
            'applied' => false,
            'reason' => null,
        ];

        // Check conditions
        if ($commissionRule->conditions) {
            // Check minimum order value
            if (isset($commissionRule->conditions->min_order_value) &&
                $validated['order_value'] < $commissionRule->conditions->min_order_value) {
                $result['reason'] = "Order value is below minimum requirement of {$commissionRule->conditions->min_order_value}";
                return response()->json($result);
            }

            // Check maximum order value
            if (isset($commissionRule->conditions->max_order_value) &&
                $validated['order_value'] > $commissionRule->conditions->max_order_value) {
                $result['reason'] = "Order value is above maximum limit of {$commissionRule->conditions->max_order_value}";
                return response()->json($result);
            }

            // Check product category
            if (isset($commissionRule->conditions->product_categories) &&
                $validated['product_category'] &&
                !in_array($validated['product_category'], $commissionRule->conditions->product_categories)) {
                $result['reason'] = "Product category is not eligible for this commission rule";
                return response()->json($result);
            }

            // Check quantity
            if (isset($commissionRule->conditions->minimum_quantity) &&
                $validated['quantity'] < $commissionRule->conditions->minimum_quantity) {
                $result['reason'] = "Quantity is below minimum requirement of {$commissionRule->conditions->minimum_quantity}";
                return response()->json($result);
            }

            if (isset($commissionRule->conditions->maximum_quantity) &&
                $validated['quantity'] > $commissionRule->conditions->maximum_quantity) {
                $result['reason'] = "Quantity is above maximum limit of {$commissionRule->conditions->maximum_quantity}";
                return response()->json($result);
            }
        }

        // Calculate commission
        $commission = $commissionRule->type === 'percentage'
            ? $validated['order_value'] * ($commissionRule->value / 100)
            : $commissionRule->value;

        $result['commission_amount'] = round($commission, 2);
        $result['applied'] = true;

        return response()->json($result);
    }

    /**
     * Test the commission rule against historical orders.
     */
    public function testHistorical(CommissionRule $commissionRule)
    {
        // Get last 10 orders
        $orders = Order::with('items')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) use ($commissionRule) {
                $commission = $commissionRule->type === 'percentage'
                    ? $order->total * ($commissionRule->value / 100)
                    : $commissionRule->value;

                return [
                    'id' => $order->id,
                    'date' => $order->created_at->format('Y-m-d'),
                    'total' => $order->total,
                    'items_count' => $order->items->count(),
                    'would_apply' => $this->checkConditions($order, $commissionRule),
                    'potential_commission' => round($commission, 2),
                ];
            });

        return response()->json([
            'orders' => $orders,
            'summary' => [
                'total_orders' => $orders->count(),
                'applicable_orders' => $orders->where('would_apply', true)->count(),
                'total_potential_commission' => $orders->where('would_apply', true)->sum('potential_commission'),
            ],
        ]);
    }

    /**
     * Check if an order meets the commission rule conditions.
     */
    private function checkConditions(Order $order, CommissionRule $commissionRule): bool
    {
        if (!$commissionRule->conditions) {
            return true;
        }

        $conditions = $commissionRule->conditions;

        // Check order value conditions
        if (isset($conditions->min_order_value) && $order->total < $conditions->min_order_value) {
            return false;
        }

        if (isset($conditions->max_order_value) && $order->total > $conditions->max_order_value) {
            return false;
        }

        // Check quantity conditions
        $totalQuantity = $order->items->sum('quantity');
        if (isset($conditions->minimum_quantity) && $totalQuantity < $conditions->minimum_quantity) {
            return false;
        }

        if (isset($conditions->maximum_quantity) && $totalQuantity > $conditions->maximum_quantity) {
            return false;
        }

        // Check product categories
        if (isset($conditions->product_categories)) {
            $orderCategories = $order->items->pluck('product.category_id')->unique();
            $hasMatchingCategory = $orderCategories->intersect($conditions->product_categories)->isNotEmpty();
            if (!$hasMatchingCategory) {
                return false;
            }
        }

        return true;
    }
}
