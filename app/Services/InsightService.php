<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class InsightService
{
    public function getTopSellingProducts($limit = 5)
    {
        return Product::select('products.*',
            DB::raw('COUNT(order_items.id) as total_orders'),
            DB::raw('SUM(order_items.original_price) as total_revenue'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.bpom_code',
                     'products.bpom_reference_id', 'products.price', 'products.stock', 'products.reorder_point',
                     'products.is_by_order', 'products.description', 'products.created_at',
                     'products.updated_at', 'products.deleted_at', 'products.commission_rate')
            ->orderBy('total_orders', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLowStockProducts($limit = 5)
    {
        return Product::where('stock', '<=', DB::raw('reorder_point'))
            ->orderBy('stock', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getTopCategories($limit = 5)
    {
        return Category::select('categories.*',
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(order_items.original_price) as total_revenue'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('categories.id')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSalesAnalytics($period = 'monthly')
    {
        $query = Order::select(
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(*) as order_count')
        )->where('status', 'completed');

        if ($period === 'daily') {
            $query->addSelect(DB::raw('DATE(created_at) as date'))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30);
        } elseif ($period === 'monthly') {
            $query->addSelect(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12);
        } elseif ($period === 'yearly') {
            $query->addSelect(DB::raw('YEAR(created_at) as year'))
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->limit(5);
        }

        return $query->get();
    }

    public function getCustomerTypeAnalytics()
    {
        return Order::select(
            'customer_type',
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(total) as total_revenue')
        )
        ->where('status', 'completed')
        ->groupBy('customer_type')
        ->orderBy('total_revenue', 'desc')
        ->get();
    }

    public function getPriceAdjustmentImpact()
    {
        return Order::select(
            DB::raw('SUM(order_items.original_price - order_items.adjusted_price) as total_discount'),
            DB::raw('COUNT(DISTINCT orders.id) as affected_orders'),
            'order_items.adjustment_reason'
        )
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->whereNotNull('order_items.adjusted_price')
        ->whereNotNull('order_items.adjustment_reason')
        ->where('orders.status', 'completed')
        ->groupBy('order_items.adjustment_reason')
        ->orderBy('total_discount', 'desc')
        ->get();
    }

    public function getBPOMMatchingStats()
    {
        return Product::select(
            DB::raw('COUNT(*) as total_products'),
            DB::raw('COUNT(bpom_reference_id) as matched_products'),
            DB::raw('COUNT(CASE WHEN bpom_reference_id IS NULL THEN 1 END) as unmatched_products')
        )->first();
    }
}
