<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Commission;
use App\Models\ProfitDistribution;

class ReportService
{
    public function getRevenueReport($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total');
    }

    public function getCommissionReport($userId, $startDate, $endDate)
    {
        return Commission::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getProfitDistributionReport($startDate, $endDate)
    {
        return ProfitDistribution::whereBetween('distribution_date', [$startDate, $endDate])
            ->get();
    }
}
