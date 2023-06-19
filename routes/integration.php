<?php

use App\Facades\Pipedrive;
use App\Http\Controllers\PipedriveAuthController;
use App\Http\Controllers\SalesforceAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('salesforce-auth', [SalesforceAuthController::class, 'create'])->name('authenticate.salesforce.create');
    Route::get('salesforce-callback', [SalesforceAuthController::class, 'store'])->name('authenticate.salesforce.store');
    Route::get('salesforce-test', function () {
        return back()->withErrors([
            'implementation' => 'not yet implemented',
        ]);
    })->name('salesforce.test');

    Route::get('pipedrive-auth', [PipedriveAuthController::class, 'create'])->name('authenticate.pipedrive.create');
    Route::get('pipedrive-callback', [PipedriveAuthController::class, 'store'])->name('authenticate.pipedrive.store');

    Route::get('pipedrive-test', function () {
        $deals = Pipedrive::deals()->all();

        if ($deals->isSuccess()) {
            return back();
        } else {
            throw new Exception('Error Processing Request', 1);
        }
    })->name('pipedrive.test');
});
