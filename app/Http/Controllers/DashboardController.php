<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Commission;
use App\Models\ProfitDistribution;
use App\Services\InsightService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService)
    {
        $this->middleware('auth');
        $this->insightService = $insightService;
    }

    public function index()
    {
        // Get statistics
        $stats = [
            'total_sales' => Order::where('status', 'completed')
                ->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::whereIn('status', ['new', 'in_progress'])
                ->count(),
            'low_stock_products' => Product::where('stock', '<=', DB::raw('reorder_point'))
                ->count()
        ];

        // Get recent orders
        $recentOrders = Order::with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        // Get top selling products
        $topProducts = $this->insightService->getTopSellingProducts(5);

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get commission summary
        $commissionSummary = Commission::select(
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(*) as total_count')
        )->first();

        // Get profit distribution summary
        $profitSummary = ProfitDistribution::select(
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(DISTINCT order_id) as orders_count')
        )->first();

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'recentActivities',
            'commissionSummary',
            'profitSummary'
        ));
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Add recent orders
        $orders = Order::latest()
            ->take(3)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'icon' => 'shopping-cart',
                    'color' => 'blue',
                    'message' => "New order #{$order->id} received",
                    'details' => "Total: $" . number_format($order->total, 2),
                    'time' => $order->created_at
                ];
            });
        $activities = $activities->concat($orders);

        // Add low stock alerts
        $lowStock = Product::where('stock', '<=', DB::raw('reorder_point'))
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'stock',
                    'icon' => 'exclamation-triangle',
                    'color' => 'yellow',
                    'message' => "Low stock alert for {$product->name}",
                    'details' => "Current stock: {$product->stock}",
                    'time' => now()
                ];
            });
        $activities = $activities->concat($lowStock);

        // Add recent profit distributions
        $profits = ProfitDistribution::latest()
            ->take(3)
            ->get()
            ->map(function ($distribution) {
                return [
                    'type' => 'profit',
                    'icon' => 'chart-pie',
                    'color' => 'green',
                    'message' => "Profit distribution processed",
                    'details' => "Amount: $" . number_format($distribution->amount, 2),
                    'time' => $distribution->created_at
                ];
            });
        $activities = $activities->concat($profits);

        return $activities->sortByDesc('time')->take(5);
    }
}
