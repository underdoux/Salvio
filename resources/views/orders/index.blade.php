<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
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
            <!-- Navigation -->
            <nav class="bg-white shadow-sm mb-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="sr-only">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800">Orders</h2>
                        <a href="{{ route('orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>New Order
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($order->total, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($order->payment_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="{{ route('orders.edit', $order) }}" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No orders found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
