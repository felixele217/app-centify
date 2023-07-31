<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AppendTimeScopeQuery;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => to_route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::middleware(AppendTimeScopeQuery::class)->get('/dashboard', DashboardController::class)->name('dashboard');
   
    Route::get('/integrations', IntegrationController::class)->name('integrations');

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

    Route::prefix('deals')->name('deals.')->controller(DealController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::prefix('deals')->name('deals.')->controller(DealController::class)->group(function () {
        Route::put('/{deal}', 'update')->name('update');
    });

    Route::prefix('agents')->name('agents.')->controller(AgentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{agent}', 'update')->name('update');
        Route::delete('/{agent}', 'destroy')->name('destroy');
    });

});

require __DIR__.'/auth.php';
require __DIR__.'/integration.php';
