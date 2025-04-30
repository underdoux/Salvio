@extends('layouts.app')

@section('title', 'Business Insights')

@section('header', 'Business Insights')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endpush

@section('content')
<div class="space-y-6">
    <!-- Period Selector -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Sales Analytics</h2>
            <div class="flex space-x-2">
                <a href="{{ route('insights.index', ['period' => 'daily']) }}" 
                   class="px-3 py-1 rounded {{ $selectedPeriod === 'daily' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Daily
                </a>
                <a href="{{ route('insights.index', ['period' => 'monthly']) }}" 
                   class="px-3 py-1 rounded {{ $selectedPeriod === 'monthly' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Monthly
                </a>
                <a href="{{ route('insights.index', ['period' => 'yearly']) }}" 
                   class="px-3 py-1 rounded {{ $selectedPeriod === 'yearly' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Yearly
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sales Trend -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Trend</h3>
            <canvas id="salesTrendChart" height="300"></canvas>
        </div>

        <!-- Product Performance -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products</h3>
            <canvas id="productPerformanceChart" height="300"></canvas>
        </div>

        <!-- Category Performance -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Performance</h3>
            <canvas id="categoryPerformanceChart" height="300"></canvas>
        </div>

        <!-- Customer Type Analysis -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Type Analysis</h3>
            <canvas id="customerAnalyticsChart" height="300"></canvas>
        </div>
    </div>

    <!-- Additional Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Price Adjustments Impact -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Price Adjustments Impact</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Discount</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Affected Orders</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($priceAdjustments as $adjustment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $adjustment->adjustment_reason }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($adjustment->total_discount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $adjustment->affected_orders }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BPOM Matching Stats -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">BPOM Product Matching</h3>
            <div class="flex items-center justify-center h-64">
                <canvas id="bpomMatchingChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Products</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $bpomStats->total_products }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Matched</p>
                    <p class="text-lg font-semibold text-green-600">{{ $bpomStats->matched_products }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Unmatched</p>
                    <p class="text-lg font-semibold text-red-600">{{ $bpomStats->unmatched_products }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Trend Chart
    fetch('{{ route("insights.salesTrend", ["period" => $selectedPeriod]) }}')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('salesTrendChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Sales',
                        data: data.sales,
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Product Performance Chart
    fetch('{{ route("insights.productPerformance") }}')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('productPerformanceChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Orders',
                        data: data.orders,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Category Performance Chart
    fetch('{{ route("insights.categoryPerformance") }}')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('categoryPerformanceChart'), {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.revenue,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.5)',
                            'rgba(16, 185, 129, 0.5)',
                            'rgba(245, 158, 11, 0.5)',
                            'rgba(239, 68, 68, 0.5)',
                            'rgba(139, 92, 246, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Customer Analytics Chart
    fetch('{{ route("insights.customerAnalytics") }}')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('customerAnalyticsChart'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.revenue,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.5)',
                            'rgba(16, 185, 129, 0.5)',
                            'rgba(245, 158, 11, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // BPOM Matching Chart
    fetch('{{ route("insights.bpomMatching") }}')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('bpomMatchingChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Matched', 'Unmatched'],
                    datasets: [{
                        data: [data.matched, data.unmatched],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.5)',
                            'rgba(239, 68, 68, 0.5)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
});
</script>
@endpush
