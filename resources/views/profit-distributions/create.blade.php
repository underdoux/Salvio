@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Calculate New Profit Distribution</h1>
        <a href="{{ route('profit-distributions.index') }}" class="text-gray-600 hover:text-gray-900">
            Back to List
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('profit-distributions.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Period Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date
                    </label>
                    <input type="date"
                           name="start_date"
                           value="{{ old('start_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        End Date
                    </label>
                    <input type="date"
                           name="end_date"
                           value="{{ old('end_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Important Notes -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-md">
                <h3 class="text-lg font-medium text-yellow-800 mb-2">Important Notes</h3>
                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                    <li>Only completed and paid orders within the selected period will be included in the calculation</li>
                    <li>The calculation includes:
                        <ul class="list-disc list-inside ml-4 mt-1">
                            <li>Total revenue from orders</li>
                            <li>Product costs</li>
                            <li>Operational costs</li>
                            <li>Commissions paid</li>
                            <li>Taxes</li>
                        </ul>
                    </li>
                    <li>Profit shares will be calculated based on each investor's capital percentage</li>
                    <li>All distributions will be created with 'pending' status and need approval</li>
                </ul>
            </div>

            <!-- Confirmation -->
            <div class="mt-6">
                <div class="relative flex items-start">
                    <div class="flex h-5 items-center">
                        <input type="checkbox"
                               name="confirm"
                               required
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="confirm" class="font-medium text-gray-700">
                            I confirm that the selected period is correct and understand that this action cannot be undone
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Calculate Distribution
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Active Investors</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Investor Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Investment Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Share Percentage
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(\App\Models\CapitalInvestor::where('is_active', true)->get() as $investor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $investor->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rp {{ number_format($investor->investment_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format(($investor->investment_amount / \App\Models\CapitalInvestor::where('is_active', true)->sum('investment_amount')) * 100, 2) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any JavaScript validation or enhancement here
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');

    // Set min/max dates if needed
    const today = new Date().toISOString().split('T')[0];
    endDate.setAttribute('max', today);

    startDate.addEventListener('change', function() {
        endDate.setAttribute('min', this.value);
    });
});
</script>
@endpush
@endsection
