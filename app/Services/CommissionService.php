<?php

namespace App\Services;

use App\Models\Commission;

class CommissionService
{
    public function calculateCommission($productId, $categoryId, $originalPrice)
    {
        $commission = Commission::where('product_id', $productId)->first();

        if (!$commission) {
            $commission = Commission::where('category_id', $categoryId)->first();
        }

        if (!$commission) {
            $commission = Commission::whereNull('product_id')->whereNull('category_id')->first();
        }

        if (!$commission) {
            return 0;
        }

        $rate = $commission->commission_rate;
        $minCap = $commission->min_cap;
        $maxCap = $commission->max_cap;

        $calculated = $originalPrice * $rate / 100;

        if ($minCap !== null && $calculated < $minCap) {
            $calculated = $minCap;
        }

        if ($maxCap !== null && $calculated > $maxCap) {
            $calculated = $maxCap;
        }

        return $calculated;
    }
}
