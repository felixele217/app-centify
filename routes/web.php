<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', fn () => to_route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/plans', fn () => Inertia::render('Plans'))->name('plans');
    Route::get('/integrations', fn () => Inertia::render('Integrations'))->name('integrations');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::post('', 'store')->name('store');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/integration.php';
