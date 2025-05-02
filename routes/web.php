<?php

use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CommissionRuleController;
use App\Http\Controllers\CommissionRuleConflictController;
use App\Http\Controllers\CommissionRuleDependencyController;
use App\Http\Controllers\CommissionRulePreviewController;
use App\Http\Controllers\CommissionRuleVersionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products
    Route::resource('products', ProductController::class);

    // Orders
    Route::resource('orders', OrderController::class);

    // Commission Rules
    Route::resource('commission-rules', CommissionRuleController::class);
    Route::post('commission-rules/{commissionRule}/duplicate', [CommissionRuleController::class, 'duplicate'])
        ->name('commission-rules.duplicate');
    Route::post('commission-rules/{commissionRule}/save-as-template', [CommissionRuleController::class, 'saveAsTemplate'])
        ->name('commission-rules.save-as-template');
    Route::post('commission-rules/{template}/create-from-template', [CommissionRuleController::class, 'createFromTemplate'])
        ->name('commission-rules.create-from-template');

    // Commission Rule Dependencies
    Route::get('commission-rules/{commissionRule}/dependencies', [CommissionRuleDependencyController::class, 'index'])
        ->name('commission-rules.dependencies.index');
    Route::get('commission-rules/{commissionRule}/dependencies/graph', [CommissionRuleDependencyController::class, 'graph'])
        ->name('commission-rules.dependencies.graph');
    Route::get('commission-rules/{commissionRule}/dependencies/analyze', [CommissionRuleDependencyController::class, 'analyze'])
        ->name('commission-rules.dependencies.analyze');
    Route::post('commission-rules/{commissionRule}/dependencies', [CommissionRuleDependencyController::class, 'store'])
        ->name('commission-rules.dependencies.store');
    Route::delete('commission-rules/{commissionRule}/dependencies/{dependency}', [CommissionRuleDependencyController::class, 'destroy'])
        ->name('commission-rules.dependencies.destroy');
    Route::post('commission-rules/{commissionRule}/dependencies/validate', [CommissionRuleDependencyController::class, 'validate'])
        ->name('commission-rules.dependencies.validate');

    // Commission Rule Conflicts
    Route::get('commission-rules/conflicts', [CommissionRuleConflictController::class, 'index'])
        ->name('commission-rules.conflicts.index');
    Route::get('commission-rules/conflicts/{conflict}', [CommissionRuleConflictController::class, 'show'])
        ->name('commission-rules.conflicts.show');
    Route::post('commission-rules/conflicts/{conflict}/resolve', [CommissionRuleConflictController::class, 'resolve'])
        ->name('commission-rules.conflicts.resolve');
    Route::post('commission-rules/conflicts/detect-all', [CommissionRuleConflictController::class, 'detectAll'])
        ->name('commission-rules.conflicts.detect-all');
    Route::post('commission-rules/{commissionRule}/conflicts/detect', [CommissionRuleConflictController::class, 'detect'])
        ->name('commission-rules.conflicts.detect');

    // Commission Rule Versions
    Route::get('commission-rules/{commissionRule}/versions', [CommissionRuleVersionController::class, 'index'])
        ->name('commission-rules.versions.index');
    Route::get('commission-rules/{commissionRule}/versions/{version}', [CommissionRuleVersionController::class, 'show'])
        ->name('commission-rules.versions.show');
    Route::get('commission-rules/{commissionRule}/versions/{versionA}/compare/{versionB}', [CommissionRuleVersionController::class, 'compare'])
        ->name('commission-rules.versions.compare');
    Route::post('commission-rules/{commissionRule}/versions/{version}/restore', [CommissionRuleVersionController::class, 'restore'])
        ->name('commission-rules.versions.restore');

    // Commission Rule Preview
    Route::get('commission-rules/{commissionRule}/preview', [CommissionRulePreviewController::class, 'show'])
        ->name('commission-rules.preview.show');
    Route::post('commission-rules/{commissionRule}/preview/simulate', [CommissionRulePreviewController::class, 'simulate'])
        ->name('commission-rules.preview.simulate');

    // Commissions
    Route::resource('commissions', CommissionController::class)->only(['index', 'show']);

    // Insights
    Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
    Route::get('/insights/{type}', [InsightController::class, 'show'])->name('insights.show');
});

require __DIR__.'/auth.php';
