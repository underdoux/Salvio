<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CapitalInvestor;
use App\Models\ProfitDistribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfitDistributionService
{
    public function calculateProfitDistribution($startDate, $endDate)
    {
        return DB::transaction(function () use ($startDate, $endDate) {
            // Get completed and paid orders for the period
            $orders = Order::where('status', Order::STATUS_PAID)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Calculate total revenue
            $totalRevenue = $orders->sum('total');

            // Calculate total product cost
            $totalProductCost = $orders->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return $item->product->cost_price * $item->quantity;
                });
            });

            // Get total operational costs
            $totalOperationalCost = $orders->sum('operational_cost');

            // Calculate total commissions paid
            $totalCommissions = $orders->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return optional($item->commission)->amount ?? 0;
                });
            });

            // Calculate taxes
            $totalTax = $orders->sum(function ($order) {
                return $order->total * ($order->tax / 100);
            });

            // Calculate net profit
            $netProfit = $totalRevenue - $totalProductCost - $totalOperationalCost - $totalCommissions - $totalTax;

            // Get all investors
            $investors = CapitalInvestor::where('is_active', true)->get();
            $totalInvestment = $investors->sum('investment_amount');

            // Create profit distributions
            $distributions = [];
            foreach ($investors as $investor) {
                $sharePercentage = ($investor->investment_amount / $totalInvestment) * 100;
                $profitShare = ($netProfit * $sharePercentage) / 100;

                $distribution = ProfitDistribution::create([
                    'capital_investor_id' => $investor->id,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'total_revenue' => $totalRevenue,
                    'total_cost' => $totalProductCost + $totalOperationalCost + $totalCommissions + $totalTax,
                    'net_profit' => $netProfit,
                    'share_percentage' => $sharePercentage,
                    'profit_share' => $profitShare,
                    'status' => 'pending'
                ]);

                $distributions[] = $distribution;
            }

            return [
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_product_cost' => $totalProductCost,
                    'total_operational_cost' => $totalOperationalCost,
                    'total_commissions' => $totalCommissions,
                    'total_tax' => $totalTax,
                    'net_profit' => $netProfit
                ],
                'distributions' => $distributions
            ];
        });
    }

    public function approveProfitDistribution(ProfitDistribution $distribution)
    {
        return DB::transaction(function () use ($distribution) {
            $distribution->update([
                'status' => 'approved',
                'approved_at' => Carbon::now(),
                'approved_by' => Auth::id()
            ]);

            // You might want to trigger notifications here
            return $distribution;
        });
    }

    public function getProfitDistributionSummary($year = null, $month = null)
    {
        $query = ProfitDistribution::with('capitalInvestor');

        if ($year && $month) {
            $query->whereYear('period_start', $year)
                  ->whereMonth('period_start', $month);
        }

        $distributions = $query->get();

        return [
            'total_distributed' => $distributions->where('status', 'approved')->sum('profit_share'),
            'total_pending' => $distributions->where('status', 'pending')->sum('profit_share'),
            'distribution_count' => $distributions->count(),
            'approved_count' => $distributions->where('status', 'approved')->count(),
            'pending_count' => $distributions->where('status', 'pending')->count(),
            'by_investor' => $distributions->groupBy('capital_investor_id')->map(function ($group) {
                return [
                    'investor_name' => $group->first()->capitalInvestor->name,
                    'total_share' => $group->sum('profit_share'),
                    'share_percentage' => $group->first()->share_percentage,
                    'approved_amount' => $group->where('status', 'approved')->sum('profit_share'),
                    'pending_amount' => $group->where('status', 'pending')->sum('profit_share')
                ];
            })
        ];
    }

    public function getMonthlyProfitTrend($year = null)
    {
        $year = $year ?? date('Y');

        return ProfitDistribution::selectRaw('
                MONTH(period_start) as month,
                SUM(total_revenue) as revenue,
                SUM(total_cost) as cost,
                SUM(net_profit) as profit,
                COUNT(*) as distribution_count
            ')
            ->whereYear('period_start', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::create()->month($item->month)->format('F');
                return $item;
            });
    }
}
