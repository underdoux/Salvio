<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    use App\Http\Controllers\ProductController;

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/orders', function () {
        return view('orders.index');
    })->name('orders.index');

    Route::get('/commissions', function () {
        return view('commissions.index');
    })->name('commissions.index');

    Route::get('/profit-distributions', function () {
        return view('profit-distributions.index');
    })->name('profit-distributions.index');

    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/insights', function () {
        return view('insights.index');
    })->name('insights.index');
});

require __DIR__.'/auth.php';
