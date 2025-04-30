@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Profit Distribution</h1>
        <div class="flex space-x-4">
            <a href="{{ route('profit-distributions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Calculate New Distribution
            </a>
            <a href="{{ route('profit-distributions.export', ['year' => $year, 'month' => $month]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export CSV
            </a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('profit-distributions.index') }}" method="GET" class="flex space-x-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Year</label>
                <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Month</label>
                <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Total Distributed</h3>
            <p class="mt-2 text-3xl font-semibold text-green-600">
                Rp {{ number_format($summary['total_distributed'], 2) }}
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Pending Distribution</h3>
            <p class="mt-2 text-3xl font-semibold text-yellow-600">
                Rp {{ number_format($summary['total_pending'], 2) }}
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900">Distribution Count</h3>
            <p class="mt-2 text-3xl font-semibold text-blue-600">
                {{ $summary['distribution_count'] }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $summary['approved_count'] }} approved, {{ $summary['pending_count'] }} pending
            </p>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Monthly Profit Trend</h3>
        <div class="h-64">
            <canvas id="profitTrendChart"></canvas>
        </div>
    </div>

    <!-- Distributions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Profit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Share %</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($distributions as $distribution)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $distribution->period_start->format('F Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $distribution->capitalInvestor->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($distribution->net_profit, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($distribution->share_percentage, 2) }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($distribution->profit_share, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $distribution->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($distribution->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('profit-distributions.show', $distribution) }}"
                           class="text-indigo-600 hover:text-indigo-900 mr-3">
                            View
                        </a>
                        @if($distribution->status === 'pending')
                        <form action="{{ route('profit-distributions.approve', $distribution) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-green-600 hover:text-green-900">
                                Approve
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $distributions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('profitTrendChart').getContext('2d');
    const trend = @json($trend);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trend.map(item => item.month_name),
            datasets: [
                {
                    label: 'Revenue',
                    data: trend.map(item => item.revenue),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true
                },
                {
                    label: 'Cost',
                    data: trend.map(item => item.cost),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true
                },
                {
                    label: 'Profit',
                    data: trend.map(item => item.profit),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true
                }
            ]
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
</script>
@endpush
@endsection
