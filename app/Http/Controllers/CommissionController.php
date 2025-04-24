<?php

namespace App\Http\Controllers;

use App\Services\CommissionService;

class CommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        parent::__construct(); // Initialize parent controller
        $this->commissionService = $commissionService;
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function index()
    {
        return view('commissions.index');
    }
}
