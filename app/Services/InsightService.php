<?php

namespace App\Services;

use App\Models\OrderItem;

class InsightService
{
    public function getBestSellingProducts($startDate, $endDate)
    {
        return OrderItem::select('product_id')
            ->with('product')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'paid');
            })
            ->groupBy('product_id')
            ->orderByRaw('SUM(quantity) DESC')
            ->get();
    }

    public function getLeastSellingProducts($startDate, $endDate)
    {
        return OrderItem::select('product_id')
            ->with('product')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'paid');
            })
            ->groupBy('product_id')
            ->orderByRaw('SUM(quantity) ASC')
            ->get();
    }

    public function getMarketResponseByCustomerType($startDate, $endDate)
    {
        // Placeholder for market response logic
        return [];
    }
}
