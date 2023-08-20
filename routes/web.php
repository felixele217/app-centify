<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealRejectionController;
use App\Http\Controllers\DeployedVersionController;
use App\Http\Controllers\PaidLeaveController;
use App\Http\Controllers\PayoutsExportController;
use App\Http\Controllers\PlanAgentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SplitController;
use App\Http\Middleware\AppendTimeScopeQuery;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('dashboard'));
Route::get('/deployed-version', DeployedVersionController::class)->name('deployed-version');

Route::middleware('auth')->group(function () {
    Route::middleware(AppendTimeScopeQuery::class)->get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/payouts-export', PayoutsExportController::class)->name('payouts-export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('plans')->name('plans.')->controller(PlanController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{plan}', 'edit')->name('edit');
        Route::put('/{plan}', 'update')->name('update');
        Route::delete('/{plan}', 'destroy')->name('destroy');
    });

    Route::prefix('plans')->name('plans.agents.')->controller(PlanAgentController::class)->group(function () {
        Route::post('/{plan}/agents/{agent}', 'store')->name('store');
        Route::delete('/{plan}/agents/{agent}', 'destroy')->name('destroy');
    });

    Route::prefix('deals')->name('deals.')->controller(DealController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{deal}', 'update')->name('update');

        Route::prefix('{deal}/rejections')->name('rejections.')->controller(DealRejectionController::class)->group(function () {
            Route::post('', 'store')->name('store');
        });

        Route::prefix('{deal}/splits')->name('splits.')->controller(SplitController::class)->group(function () {
            Route::post('', 'store')->name('store');
        });
    });

    Route::prefix('agents')->name('agents.')->controller(AgentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{agent}', 'update')->name('update');
        Route::delete('/{agent}', 'destroy')->name('destroy');

        Route::prefix('{agent}/paid-leaves')->name('paid-leaves.')->controller(PaidLeaveController::class)->group(function () {
            Route::post('/', 'store')->name('store');
            Route::delete('/{paidLeave}', 'destroy')->name('destroy');
        });
    });

});

require __DIR__.'/auth.php';
require __DIR__.'/integration.php';
