<?php

use App\Http\Controllers\CustomIntegrationFieldController;
use App\Http\Controllers\PipedriveAuthController;
use App\Http\Controllers\SalesforceAuthController;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
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

    Route::prefix('custom-integration-fields')->name('custom-integration-fields.')->controller(CustomIntegrationFieldController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{customIntegrationField}', 'update')->name('update');
    });

    Route::get('pipedrive-sync', function () {
        (new PipedriveIntegrationService())->syncAgentDeals();

        return back();
    })->name('pipedrive.sync');
});
