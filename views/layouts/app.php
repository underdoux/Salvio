<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Salvio' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-800 min-h-screen">
        <div class="p-4">
            <h1 class="text-2xl font-bold text-indigo-400">Salvio</h1>
        </div>
        <nav class="mt-8">
            <div class="px-4 mb-4">
                <p class="text-sm text-slate-400">MAIN MENU</p>
            </div>
            <a href="/admin/dashboard" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/products" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-box w-6"></i>
                <span>Products</span>
            </a>
            <a href="/admin/sales" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-chart-line w-6"></i>
                <span>Sales</span>
            </a>
            <a href="/admin/orders" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-shopping-cart w-6"></i>
                <span>Orders</span>
            </a>
            <div class="px-4 mt-8 mb-4">
                <p class="text-sm text-slate-400">SETTINGS</p>
            </div>
            <a href="/admin/users" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-users w-6"></i>
                <span>Users</span>
            </a>
            <a href="/admin/settings" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white">
                <i class="fas fa-cog w-6"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Navigation -->
        <header class="bg-slate-800 shadow-lg">
            <div class="flex items-center justify-between px-8 py-4">
                <div class="flex items-center space-x-4">
                    <button id="mobile-menu-button" class="text-slate-300 lg:hidden">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-slate-300 hover:text-white">
                            <span><?= htmlspecialchars($user['username']) ?></span>
                            <i class="fas fa-user-circle text-xl"></i>
                        </button>
                    </div>
                    <a href="/logout" class="text-slate-300 hover:text-white">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-8">
            <?= $content ?? '' ?>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.querySelector('aside');
        
        mobileMenuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Add responsive classes for mobile
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full', 'fixed', 'top-0', 'left-0', 'z-40', 'transition-transform', 'duration-300');
        }

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full', 'fixed', 'top-0', 'left-0', 'z-40', 'transition-transform', 'duration-300');
            } else {
                sidebar.classList.add('-translate-x-full', 'fixed', 'top-0', 'left-0', 'z-40', 'transition-transform', 'duration-300');
            }
        });
    </script>
</body>
</html>
