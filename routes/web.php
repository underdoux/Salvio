<?php

use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfitDistributionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Commission Management Routes
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::get('/summary', [CommissionController::class, 'summary'])->name('summary');
        Route::get('/export', [CommissionController::class, 'export'])
            ->middleware('role:Admin')
            ->name('export');

        // Commission Rules Management (Admin only)
        Route::middleware(['role:Admin'])->group(function () {
            Route::get('/rules', [CommissionController::class, 'rules'])->name('rules');
            Route::post('/rules', [CommissionController::class, 'createRule'])->name('rules.create');
            Route::put('/rules', [CommissionController::class, 'updateRules'])->name('rules.update');
            Route::delete('/rules/{rule}', [CommissionController::class, 'deleteRule'])->name('rules.delete');
        });
    });

    // Order Management Routes
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Product Management Routes
    Route::resource('products', ProductController::class);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profit Distribution Routes (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('profit-distributions', ProfitDistributionController::class);
    });

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('commissions');
        Route::get('/price-adjustments', [ReportController::class, 'priceAdjustments'])->name('price-adjustments');
        Route::get('/profit-distributions', [ReportController::class, 'profitDistributions'])
            ->middleware('role:Admin')
            ->name('profit-distributions');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Insight Routes
    Route::prefix('insights')->name('insights.')->group(function () {
        Route::get('/', [InsightController::class, 'index'])->name('index');
        Route::get('/sales', [InsightController::class, 'sales'])->name('sales');
        Route::get('/products', [InsightController::class, 'products'])->name('products');
        Route::get('/categories', [InsightController::class, 'categories'])->name('categories');
        Route::get('/customers', [InsightController::class, 'customers'])->name('customers');
    });

    // Settings Routes (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/currency', [SettingsController::class, 'currency'])->name('settings.currency');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
