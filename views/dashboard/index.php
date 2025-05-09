<?php
$title = 'Dashboard - Salvio';
ob_start();
?>

<h2 class="text-2xl font-semibold mb-6">Dashboard Overview</h2>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Sales Today</h3>
            <i class="fas fa-chart-bar text-indigo-400 text-2xl"></i>
        </div>
        <p class="text-3xl font-bold">0</p>
    </div>
    
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Products in Stock</h3>
            <i class="fas fa-box text-indigo-400 text-2xl"></i>
        </div>
        <p class="text-3xl font-bold">0</p>
    </div>
    
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Pending Orders</h3>
            <i class="fas fa-clock text-indigo-400 text-2xl"></i>
        </div>
        <p class="text-3xl font-bold">0</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
