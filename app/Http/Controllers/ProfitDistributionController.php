<?php

namespace App\Http\Controllers;

use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;

class ProfitDistributionController extends Controller
{
    protected $profitDistributionService;

    public function __construct(ProfitDistributionService $profitDistributionService)
    {
        $this->profitDistributionService = $profitDistributionService;
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function index()
    {
        return view('profit-distributions.index');
    }

    public function distribute(Request $request)
    {
        $this->profitDistributionService->distributeMonthlyProfit();

        return redirect()->route('profit-distributions.index')
            ->with('success', 'Profit distribution completed successfully.');
    }
}
