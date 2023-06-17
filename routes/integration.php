<?php

use App\Facades\Pipedrive;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PipedriveAuthController;
use App\Http\Controllers\SalesforceAuthController;

Route::middleware('auth')->group(function () {
    Route::get('/salesforce-auth', [SalesforceAuthController::class, 'create'])->name('authenticate.salesforce.create');
    Route::get('/salesforce-callback', [SalesforceAuthController::class, 'store'])->name('authenticate.salesforce.store');

    Route::get('pipedrive-auth', [PipedriveAuthController::class, 'create'])->name('authenticate.pipedrive.create');
    Route::get('pipedrive-callback', [PipedriveAuthController::class, 'store'])->name('authenticate.pipedrive.store');

    Route::get('pipedrive-test', function () {
        $deals = Pipedrive::deals()->all();

        dd($deals->isSuccess(), $deals->getData());
    })->name('pipedrive.test');
});
