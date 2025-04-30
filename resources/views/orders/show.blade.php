<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (same as index.blade.php) -->
        <aside class="w-64 bg-white shadow-md">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Salvio Admin</h2>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Products</a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 rounded bg-blue-100 text-blue-700 font-semibold">Orders</a>
                    <a href="{{ route('commissions.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Commissions</a>
                    <a href="{{ route('profit-distributions.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Profit Distributions</a>
                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Notifications</a>
                    <a href="{{ route('reports.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Reports</a>
                    <a href="{{ route('insights.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Insights</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Order #{{ $order->id }}</h1>
                        <p class="mt-1 text-sm text-gray-600">Created on {{ $order->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('orders.edit', $order) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-edit mr-2"></i>Edit Order
                        </a>
                        <a href="{{ route('orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Order Details -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Information</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status === 'new') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'in_progress') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'paid') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Payment Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ ucfirst($order->payment_type) }}</dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Tax Rate</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->tax }}%</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${{ number_format($order->total, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adjusted Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${{ number_format($item->original_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${{ number_format($item->adjusted_price ?? $item->original_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($item->discount_percentage)
                                                {{ number_format($item->discount_percentage, 2) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->adjustment_reason ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
