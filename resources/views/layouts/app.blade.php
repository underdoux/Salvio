<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @stack('styles')
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
                    <a href="{{ route('dashboard') }}"
                       class="block px-4 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>

                    @auth
                        <a href="{{ route('products.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('products.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-box mr-2"></i>Products
                        </a>

                        <a href="{{ route('orders.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('orders.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-shopping-cart mr-2"></i>Orders
                        </a>

                        <a href="{{ route('commissions.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('commissions.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-percentage mr-2"></i>Commissions
                        </a>

                        <a href="{{ route('profit-distributions.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('profit-distributions.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-chart-pie mr-2"></i>Profit Distributions
                        </a>

                        <a href="{{ route('notifications.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('notifications.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-bell mr-2"></i>Notifications
                        </a>

                        <a href="{{ route('reports.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('reports.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-file-alt mr-2"></i>Reports
                        </a>

                        <a href="{{ route('insights.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('insights.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-chart-line mr-2"></i>Insights
                        </a>

                        @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('settings.index') }}"
                           class="block px-4 py-2 rounded {{ request()->routeIs('settings.*') ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-100 text-gray-700 hover:text-blue-700' }} font-semibold">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h1 class="text-xl font-semibold text-gray-900">@yield('header')</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-user-circle text-xl"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-6">
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

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
