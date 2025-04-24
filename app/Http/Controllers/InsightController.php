<?php

namespace App\Http\Controllers;

use App\Services\InsightService;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService)
    {
        $this->insightService = $insightService;
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $bestSelling = $this->insightService->getBestSellingProducts($startDate, $endDate);
        $leastSelling = $this->insightService->getLeastSellingProducts($startDate, $endDate);
        $marketResponse = $this->insightService->getMarketResponseByCustomerType($startDate, $endDate);

        return view('insights.index', compact('bestSelling', 'leastSelling', 'marketResponse', 'startDate', 'endDate'));
    }
}
