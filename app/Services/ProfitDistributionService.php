<?php

namespace App\Services;

use App\Models\CapitalInvestor;
use App\Models\ProfitDistribution;
use App\Models\Order;

class ProfitDistributionService
{
    public function distributeMonthlyProfit()
    {
        $paidOrders = Order::where('status', 'paid')->get();

        $totalSales = $paidOrders->sum('total');
        $totalCost = $paidOrders->sum(function ($order) {
            return $order->items->sum(function ($item) {
                return $item->product->cost * $item->quantity;
            });
        });

        $totalTax = $paidOrders->sum('tax');

        $totalExpenses = 0; // Implement expense calculation

        $netProfit = $totalSales - $totalCost - $totalTax - $totalExpenses;

        $investors = CapitalInvestor::all();

        foreach ($investors as $investor) {
            $amount = $netProfit * ($investor->ownership_percentage / 100);

            ProfitDistribution::create([
                'capital_investor_id' => $investor->id,
                'amount' => $amount,
                'distribution_date' => now(),
            ]);
        }
    }
}
