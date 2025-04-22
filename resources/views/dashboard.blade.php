<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
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
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Orders</a>
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
                    <!-- Stat Card 1 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <i class="fas fa-chart-line text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Sales</p>
                                    <p class="text-lg font-semibold text-gray-900">$24,000</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Customers</p>
                                    <p class="text-lg font-semibold text-gray-900">1,234</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 3 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Orders</p>
                                    <p class="text-lg font-semibold text-gray-900">156</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 4 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <i class="fas fa-star text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Reviews</p>
                                    <p class="text-lg font-semibold text-gray-900">4.8/5.0</p>
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
                            <!-- Activity Item -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-bell text-blue-500"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">New order received</p>
                                    <p class="text-sm text-gray-500">Order #12345 - $230.00</p>
                                    <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                </div>
                            </div>

                            <!-- Activity Item -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-check text-green-500"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Order completed</p>
                                    <p class="text-sm text-gray-500">Order #12344 has been delivered</p>
                                    <p class="text-xs text-gray-400 mt-1">4 hours ago</p>
                                </div>
                            </div>

                            <!-- Activity Item -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="fas fa-star text-yellow-500"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">New review received</p>
                                    <p class="text-sm text-gray-500">5-star review from John Doe</p>
                                    <p class="text-xs text-gray-400 mt-1">6 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-500">
                &copy; 2024 Salvio. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
