<?php

use App\Http\Controllers\Api\CommissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Commission Management
    Route::prefix('commissions')->group(function () {
        Route::get('/', [CommissionController::class, 'index']);
        Route::post('/{commission}/approve', [CommissionController::class, 'approve'])
            ->middleware('role:Admin');
        Route::post('/{commission}/reject', [CommissionController::class, 'reject'])
            ->middleware('role:Admin');
        Route::post('/{commission}/mark-paid', [CommissionController::class, 'markAsPaid'])
            ->middleware('role:Admin');
    });
});
