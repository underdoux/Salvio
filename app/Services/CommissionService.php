<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CommissionRule;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommissionService
{
    public function calculateCommission(OrderItem $item): float
    {
        $product = $item->product;
        $amount = $item->original_price; // Commission based on original price, not adjusted

        // Get applicable commission rule
        $rule = CommissionRule::getApplicableRule($product, $amount);

        if (!$rule) {
            return 0;
        }

        $commission = $amount * ($rule->rate / 100);

        // Apply min/max caps if defined
        if ($rule->min_amount && $commission < $rule->min_amount) {
            $commission = $rule->min_amount;
        }
        if ($rule->max_amount && $commission > $rule->max_amount) {
            $commission = $rule->max_amount;
        }

        return $commission;
    }

    public function processOrderCommissions(Order $order)
    {
        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $commission = $this->calculateCommission($item);

                if ($commission > 0) {
                    Commission::create([
                        'order_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'amount' => $commission,
                        'status' => 'pending',
                        'product_id' => $item->product_id,
                        'category_id' => $item->product->category_id,
                    ]);
                }
            }
        });
    }

    public function getCommissionsByUser($userId, $startDate = null, $endDate = null)
    {
        $query = Commission::where('user_id', $userId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->with(['orderItem.product', 'orderItem.order'])->get();
    }

    public function updateCommissionRules(array $rules)
    {
        return DB::transaction(function () use ($rules) {
            foreach ($rules as $rule) {
                CommissionRule::updateOrCreate(
                    [
                        'type' => $rule['type'],
                        'reference_id' => $rule['reference_id'] ?? null
                    ],
                    [
                        'rate' => $rule['rate'],
                        'min_amount' => $rule['min_amount'] ?? null,
                        'max_amount' => $rule['max_amount'] ?? null,
                        'is_active' => $rule['is_active'] ?? true
                    ]
                );
            }
        });
    }

    public function getCommissionSummary($userId, $period = 'month')
    {
        $query = Commission::where('user_id', $userId)
            ->where('status', 'approved');

        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
        }

        return [
            'total_commission' => $query->sum('amount'),
            'commission_by_category' => $query->with('orderItem.product.category')
                ->get()
                ->groupBy('orderItem.product.category.name')
                ->map(fn ($items) => $items->sum('amount')),
            'commission_count' => $query->count(),
        ];
    }
}
