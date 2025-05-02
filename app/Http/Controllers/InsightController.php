<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InsightController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->can('view insights')) {
            abort(403);
        }

        return view('insights.index', [
            'canViewSalesInsights' => $user->can('view sales insights'),
            'canViewFinancialInsights' => $user->can('view financial insights'),
            'canViewProductInsights' => $user->can('view product insights'),
            'canExport' => $user->can('export insights'),
            'canScheduleReports' => $user->can('schedule reports')
        ]);
    }

    public function financial()
    {
        $this->authorize('view financial insights');

        // Return financial insights data
        return response()->json([
            'revenue' => [],
            'profit_margins' => [],
            'expenses' => [],
            'top_revenue_sources' => []
        ]);
    }

    public function sales()
    {
        $this->authorize('view sales insights');

        // Return sales insights data
        return response()->json([
            'sales_trends' => [],
            'top_products' => [],
            'sales_by_category' => [],
            'sales_by_location' => [],
            'peak_sales_hours' => []
        ]);
    }

    public function products()
    {
        $this->authorize('view product insights');

        // Return product insights data
        return response()->json([
            'best_sellers' => [],
            'low_stock_alerts' => [],
            'product_performance' => [],
            'category_performance' => [],
            'profit_margins_by_product' => []
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('export insights');

        $type = $request->get('type', 'sales');
        $period = $request->get('period', 'monthly');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=insights_{$type}_{$period}.csv",
        ];

        $callback = function() use ($type, $period) {
            $file = fopen('php://output', 'w');

            // Write CSV headers and data based on type and period
            fputcsv($file, ['Sample', 'Data', 'For', $type, $period]);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function scheduleReport(Request $request)
    {
        $this->authorize('schedule reports');

        $validated = $request->validate([
            'type' => 'required|string',
            'frequency' => 'required|string',
            'email' => 'required|email'
        ]);

        // Save scheduled report to database (assuming ScheduledReport model exists)
        \App\Models\ScheduledReport::create($validated);

        return response()->json(['message' => 'Report scheduled successfully']);
    }
}
