<?php

namespace App\Http\Controllers;

use App\Services\InsightService;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->insightService = $insightService;
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $validPeriods = ['daily', 'monthly', 'yearly'];
        
        if (!in_array($period, $validPeriods)) {
            $period = 'monthly';
        }

        $data = [
            'topProducts' => $this->insightService->getTopSellingProducts(10),
            'lowStockProducts' => $this->insightService->getLowStockProducts(10),
            'topCategories' => $this->insightService->getTopCategories(5),
            'salesAnalytics' => $this->insightService->getSalesAnalytics($period),
            'customerTypes' => $this->insightService->getCustomerTypeAnalytics(),
            'priceAdjustments' => $this->insightService->getPriceAdjustmentImpact(),
            'bpomStats' => $this->insightService->getBPOMMatchingStats(),
            'selectedPeriod' => $period,
        ];

        return view('insights.index', $data);
    }

    public function salesTrend(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $salesData = $this->insightService->getSalesAnalytics($period);
        
        return response()->json([
            'labels' => $salesData->pluck('date')->toArray(),
            'sales' => $salesData->pluck('total_sales')->toArray(),
            'orders' => $salesData->pluck('order_count')->toArray(),
        ]);
    }

    public function productPerformance()
    {
        $topProducts = $this->insightService->getTopSellingProducts(10);
        
        return response()->json([
            'labels' => $topProducts->pluck('name')->toArray(),
            'orders' => $topProducts->pluck('total_orders')->toArray(),
            'revenue' => $topProducts->pluck('total_revenue')->toArray(),
        ]);
    }

    public function categoryPerformance()
    {
        $categories = $this->insightService->getTopCategories(10);
        
        return response()->json([
            'labels' => $categories->pluck('name')->toArray(),
            'orders' => $categories->pluck('total_orders')->toArray(),
            'revenue' => $categories->pluck('total_revenue')->toArray(),
        ]);
    }

    public function customerAnalytics()
    {
        $customerTypes = $this->insightService->getCustomerTypeAnalytics();
        
        return response()->json([
            'labels' => $customerTypes->pluck('customer_type')->toArray(),
            'orders' => $customerTypes->pluck('total_orders')->toArray(),
            'revenue' => $customerTypes->pluck('total_revenue')->toArray(),
        ]);
    }

    public function priceAdjustments()
    {
        $adjustments = $this->insightService->getPriceAdjustmentImpact();
        
        return response()->json([
            'labels' => $adjustments->pluck('adjustment_reason')->toArray(),
            'discount' => $adjustments->pluck('total_discount')->toArray(),
            'orders' => $adjustments->pluck('affected_orders')->toArray(),
        ]);
    }

    public function bpomMatching()
    {
        $stats = $this->insightService->getBPOMMatchingStats();
        
        return response()->json([
            'total' => $stats->total_products,
            'matched' => $stats->matched_products,
            'unmatched' => $stats->unmatched_products,
            'matchRate' => $stats->total_products > 0 
                ? round(($stats->matched_products / $stats->total_products) * 100, 2) 
                : 0,
        ]);
    }
}
