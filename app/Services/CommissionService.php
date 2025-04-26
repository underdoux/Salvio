<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\Product;

class CommissionService
{
    /**
     * Calculate commission based on original price.
     *
     * @param int $productId
     * @param int|null $categoryId
     * @param float $originalPrice
     * @return float
     */
    public function calculateCommission(int $productId, ?int $categoryId, float $originalPrice): float
    {
        // Get product-specific commission rule
        $productRule = CommissionRule::where('product_id', $productId)->first();

        // Get category-specific commission rule
        $categoryRule = $categoryId ? CommissionRule::where('category_id', $categoryId)->first() : null;

        // Get global commission rule
        $globalRule = CommissionRule::whereNull('product_id')->whereNull('category_id')->first();

        // Determine applicable commission rate and caps
        $rate = $globalRule ? $globalRule->global_rate : 0;
        $minCap = $globalRule ? $globalRule->min_cap : 0;
        $maxCap = $globalRule ? $globalRule->max_cap : PHP_FLOAT_MAX;

        if ($categoryRule) {
            $rate = $categoryRule->category_rate ?? $rate;
            $minCap = $categoryRule->min_cap ?? $minCap;
            $maxCap = $categoryRule->max_cap ?? $maxCap;
        }

        if ($productRule) {
            $rate = $productRule->product_rate ?? $rate;
            $minCap = $productRule->min_cap ?? $minCap;
            $maxCap = $productRule->max_cap ?? $maxCap;
        }

        // Calculate commission amount
        $commission = $originalPrice * ($rate / 100);

        // Apply min/max caps
        if ($commission < $minCap) {
            $commission = $minCap;
        } elseif ($commission > $maxCap) {
            $commission = $maxCap;
        }

        return $commission;
    }
}
