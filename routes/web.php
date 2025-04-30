<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingsController;
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

    // Products - Admin and Sales can manage products
    Route::middleware(['check.role:Admin,Sales'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    });

    // Products - Admin only operations
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Products - View access for all authenticated users
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Orders - All roles can view/process orders
    Route::resource('orders', OrderController::class);

    // Commissions - Admin manages, Sales views personal
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::resource('commissions', CommissionController::class)->except(['index', 'show']);
    });
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/{commission}', [CommissionController::class, 'show'])->name('commissions.show');

    // Payments - Admin and Cashier
    Route::middleware(['check.role:Admin,Cashier'])->group(function () {
        Route::resource('payments', PaymentController::class);
    });

    // Profit Distribution - Admin only
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::resource('profit-distributions', ProfitDistributionController::class);
    });

    // Reports - Different access levels
    Route::prefix('reports')->group(function () {
        // Admin access to all reports
        Route::middleware(['check.role:Admin'])->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        });

        // Sales reports accessible by all roles
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('reports.commissions');
    });

    // Insights - Admin only
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::prefix('insights')->group(function () {
            Route::get('/', [InsightController::class, 'index'])->name('insights.index');
            Route::get('/sales-trend', [InsightController::class, 'salesTrend'])->name('insights.salesTrend');
            Route::get('/product-performance', [InsightController::class, 'productPerformance'])->name('insights.productPerformance');
            Route::get('/category-performance', [InsightController::class, 'categoryPerformance'])->name('insights.categoryPerformance');
            Route::get('/customer-analytics', [InsightController::class, 'customerAnalytics'])->name('insights.customerAnalytics');
            Route::get('/price-adjustments', [InsightController::class, 'priceAdjustments'])->name('insights.priceAdjustments');
            Route::get('/bpom-matching', [InsightController::class, 'bpomMatching'])->name('insights.bpomMatching');
        });
    });

    // Notifications - All authenticated users
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    // Profile - All authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings - Admin only
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
