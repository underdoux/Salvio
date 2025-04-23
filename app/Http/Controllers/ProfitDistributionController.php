<?php

namespace App\Http\Controllers;

use App\Services\ProfitDistributionService;

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
}
