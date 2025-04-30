@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <div>
        <!-- Welcome Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="mt-2 text-gray-600">Here's an overview of your business operations.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Sales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Sales</p>
                            <p class="text-lg font-semibold text-gray-900">${{ number_format($stats['total_sales'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Orders</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <i class="fas fa-hourglass-half text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['pending_orders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Low Stock Products</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['low_stock_products'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @foreach($recentActivities ?? [] as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                    <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-500"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-sm text-gray-500">{{ $activity['details'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
