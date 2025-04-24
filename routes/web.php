<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfitDistributionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');

    Route::get('/profit-distributions', [App\Http\Controllers\ProfitDistributionController::class, 'index'])->name('profit-distributions.index');
    Route::post('/profit-distributions/distribute', [App\Http\Controllers\ProfitDistributionController::class, 'distribute'])->name('profit-distributions.distribute');

    Route::get('/payments/{orderId}', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{orderId}', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');

    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/insights', function () {
        return view('insights.index');
    })->name('insights.index');
});


require __DIR__.'/auth.php';
