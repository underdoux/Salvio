@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Commission Summary</h1>
        <div class="flex space-x-4">
            <select id="period" class="border rounded px-3 py-2" onchange="changePeriod(this.value)">
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
                <option value="year">This Year</option>
            </select>
            @can('access all reports')
            <a href="{{ route('commissions.export') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export CSV
            </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Commission Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-gray-500 text-sm uppercase mb-2">Total Commission</h3>
            <p class="text-3xl font-bold">Rp {{ number_format($summary['total_commission'], 2) }}</p>
        </div>

        <!-- Commission Count Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-gray-500 text-sm uppercase mb-2">Total Transactions</h3>
            <p class="text-3xl font-bold">{{ $summary['commission_count'] }}</p>
        </div>

        <!-- Average Commission Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-gray-500 text-sm uppercase mb-2">Average Commission</h3>
            <p class="text-3xl font-bold">
                Rp {{ $summary['commission_count'] > 0
                    ? number_format($summary['total_commission'] / $summary['commission_count'], 2)
                    : '0.00' }}
            </p>
        </div>
    </div>

    <!-- Commission by Category Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4">Commission by Category</h3>
        <div class="h-64">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Recent Commissions Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold mb-4">Recent Commissions</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Original Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Commission
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($commissions as $commission)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $commission->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $commission->orderItem->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $commission->orderItem->product->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($commission->orderItem->original_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($commission->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $commission->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($commission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   'bg-red-100 text-red-800') }}">
                                {{ ucfirst($commission->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($summary['commission_by_category']);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(categoryData),
            datasets: [{
                label: 'Commission Amount',
                data: Object.values(categoryData),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});

function changePeriod(period) {
    window.location.href = `{{ route('commissions.summary') }}?period=${period}`;
}
</script>
@endpush
@endsection
