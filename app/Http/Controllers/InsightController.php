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
        $this->middleware('role:Admin|Sales|Cashier');
        $this->insightService = $insightService;
    }

    public function index()
    {
        $data = [
            'topProducts' => $this->insightService->getTopSellingProducts(5),
            'topCategories' => $this->insightService->getTopCategories(5),
            'salesAnalytics' => $this->insightService->getSalesAnalytics('monthly'),
            'customerTypes' => $this->insightService->getCustomerTypeAnalytics(),
            'priceAdjustments' => $this->insightService->getPriceAdjustmentImpact(),
            'bpomStats' => $this->insightService->getBPOMMatchingStats()
        ];

        return view('insights.index', compact('data'));
    }

    public function sales()
    {
        $period = request('period', 'monthly');
        $salesData = $this->insightService->getSalesAnalytics($period);

        return view('insights.sales', compact('salesData', 'period'));
    }

    public function products()
    {
        $data = [
            'topProducts' => $this->insightService->getTopSellingProducts(10),
            'lowStock' => $this->insightService->getLowStockProducts(10)
        ];

        return view('insights.products', compact('data'));
    }

    public function categories()
    {
        $categories = $this->insightService->getTopCategories(10);

        return view('insights.categories', compact('categories'));
    }

    public function customers()
    {
        $customerData = $this->insightService->getCustomerTypeAnalytics();

        return view('insights.customers', compact('customerData'));
    }
}
