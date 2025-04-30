<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfitDistributionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InsightController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::middleware(['role:Admin|Sales'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Orders
    Route::resource('orders', OrderController::class);

    // Commissions
    Route::resource('commissions', CommissionController::class);

    // Payments
    Route::resource('payments', PaymentController::class);

    // Profit Distribution
    Route::resource('profit-distributions', ProfitDistributionController::class);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('reports.commissions');
    });

    // Insights
    Route::prefix('insights')->group(function () {
        Route::get('/', [InsightController::class, 'index'])->name('insights.index');
        Route::get('/sales-trend', [InsightController::class, 'salesTrend'])->name('insights.salesTrend');
        Route::get('/product-performance', [InsightController::class, 'productPerformance'])->name('insights.productPerformance');
        Route::get('/category-performance', [InsightController::class, 'categoryPerformance'])->name('insights.categoryPerformance');
        Route::get('/customer-analytics', [InsightController::class, 'customerAnalytics'])->name('insights.customerAnalytics');
        Route::get('/price-adjustments', [InsightController::class, 'priceAdjustments'])->name('insights.priceAdjustments');
        Route::get('/bpom-matching', [InsightController::class, 'bpomMatching'])->name('insights.bpomMatching');
    });

    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
