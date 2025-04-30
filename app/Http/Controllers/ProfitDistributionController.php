<?php

namespace App\Http\Controllers;

use App\Models\ProfitDistribution;
use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $year = request('year', date('Y'));
        $month = request('month', date('m'));

        $summary = $this->profitDistributionService->getProfitDistributionSummary($year, $month);
        $trend = $this->profitDistributionService->getMonthlyProfitTrend($year);

        $distributions = ProfitDistribution::with('capitalInvestor')
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->latest()
            ->paginate(10);

        return view('profit-distributions.index', compact('distributions', 'summary', 'trend', 'year', 'month'));
    }

    public function create()
    {
        return view('profit-distributions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            $result = $this->profitDistributionService->calculateProfitDistribution(
                Carbon::parse($validated['start_date']),
                Carbon::parse($validated['end_date'])
            );

            return redirect()
                ->route('profit-distributions.index')
                ->with('success', 'Profit distribution calculated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error calculating profit distribution: ' . $e->getMessage());
        }
    }

    public function show(ProfitDistribution $profitDistribution)
    {
        $profitDistribution->load('capitalInvestor');

        return view('profit-distributions.show', compact('profitDistribution'));
    }

    public function approve(ProfitDistribution $profitDistribution)
    {
        try {
            $this->profitDistributionService->approveProfitDistribution($profitDistribution);

            return redirect()
                ->back()
                ->with('success', 'Profit distribution approved successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error approving profit distribution: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $distributions = ProfitDistribution::with('capitalInvestor')
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=profit-distributions.csv',
        ];

        $callback = function() use ($distributions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'Period',
                'Investor',
                'Total Revenue',
                'Total Cost',
                'Net Profit',
                'Share Percentage',
                'Profit Share',
                'Status',
                'Approved At'
            ]);

            // Add data rows
            foreach ($distributions as $distribution) {
                fputcsv($file, [
                    $distribution->period_start->format('F Y'),
                    $distribution->capitalInvestor->name,
                    number_format($distribution->total_revenue, 2),
                    number_format($distribution->total_cost, 2),
                    number_format($distribution->net_profit, 2),
                    number_format($distribution->share_percentage, 2) . '%',
                    number_format($distribution->profit_share, 2),
                    ucfirst($distribution->status),
                    $distribution->approved_at ? $distribution->approved_at->format('Y-m-d H:i:s') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $trend = $this->profitDistributionService->getMonthlyProfitTrend($year);

        return view('profit-distributions.report', compact('trend', 'year'));
    }
}
