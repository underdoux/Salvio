@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Profit Distribution Details</h1>
        <div class="flex space-x-4">
            <a href="{{ route('profit-distributions.index') }}" class="text-gray-600 hover:text-gray-900">
                Back to List
            </a>
            @if($profitDistribution->status === 'pending')
            <form action="{{ route('profit-distributions.approve', $profitDistribution) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Approve Distribution
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Distribution Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution Information</h3>
            <dl class="grid grid-cols-1 gap-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Period</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $profitDistribution->period_start->format('F j, Y') }} -
                        {{ $profitDistribution->period_end->format('F j, Y') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $profitDistribution->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($profitDistribution->status) }}
                        </span>
                    </dd>
                </div>
                @if($profitDistribution->approved_at)
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Approved At</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $profitDistribution->approved_at->format('F j, Y H:i:s') }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Investor Information</h3>
            <dl class="grid grid-cols-1 gap-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Investor Name</dt>
                    <dd class="text-sm text-gray-900">{{ $profitDistribution->capitalInvestor->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Investment Amount</dt>
                    <dd class="text-sm text-gray-900">
                        Rp {{ number_format($profitDistribution->capitalInvestor->investment_amount, 2) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Share Percentage</dt>
                    <dd class="text-sm text-gray-900">
                        {{ number_format($profitDistribution->share_percentage, 2) }}%
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Financial Details -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Financial Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Revenue Section -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-4">Revenue</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Total Revenue</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                Rp {{ number_format($profitDistribution->total_revenue, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Costs Section -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-4">Costs & Deductions</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Product Costs</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                Rp {{ number_format($profitDistribution->total_cost, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-base font-medium text-gray-900">Net Profit</dt>
                        <dd class="text-base font-medium text-gray-900">
                            Rp {{ number_format($profitDistribution->net_profit, 2) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-base font-medium text-indigo-600">Profit Share Amount</dt>
                        <dd class="text-base font-medium text-indigo-600">
                            Rp {{ number_format($profitDistribution->profit_share, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if($profitDistribution->notes)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
        <p class="text-sm text-gray-600">{{ $profitDistribution->notes }}</p>
    </div>
    @endif
</div>
@endsection
