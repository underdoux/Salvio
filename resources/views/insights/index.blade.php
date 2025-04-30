@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Business Insights</h1>
        <p class="mt-2 text-gray-600">Comprehensive overview of your business performance</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Top Selling Products</h2>
            <div class="space-y-4">
                @foreach($data['topProducts'] as $product)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $product->name }}</span>
                        <span class="text-gray-600">{{ $product->total_orders }} orders</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('insights.products') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                View all products →
            </a>
        </div>

        <!-- Top Categories -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Top Categories</h2>
            <div class="space-y-4">
                @foreach($data['topCategories'] as $category)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $category->name }}</span>
                        <span class="text-gray-600">Rp {{ number_format($category->total_revenue) }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('insights.categories') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                View all categories →
            </a>
        </div>

        <!-- Sales Analytics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Monthly Sales</h2>
            <div class="space-y-4">
                @foreach($data['salesAnalytics'] as $sale)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $sale->month }}/{{ $sale->year }}</span>
                        <span class="text-gray-600">Rp {{ number_format($sale->total_sales) }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('insights.sales') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                View detailed analytics →
            </a>
        </div>

        <!-- Customer Types -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Customer Distribution</h2>
            <div class="space-y-4">
                @foreach($data['customerTypes'] as $type)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ ucfirst($type->customer_type) }}</span>
                        <span class="text-gray-600">{{ $type->total_orders }} orders</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('insights.customers') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                View customer insights →
            </a>
        </div>

        <!-- Price Adjustments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Price Adjustment Impact</h2>
            <div class="space-y-4">
                @foreach($data['priceAdjustments'] as $adjustment)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $adjustment->adjustment_reason }}</span>
                        <span class="text-gray-600">Rp {{ number_format($adjustment->total_discount) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- BPOM Matching Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">BPOM Reference Status</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Products</span>
                    <span class="text-gray-600">{{ $data['bpomStats']->total_products }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Matched Products</span>
                    <span class="text-green-600">{{ $data['bpomStats']->matched_products }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Unmatched Products</span>
                    <span class="text-red-600">{{ $data['bpomStats']->unmatched_products }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
