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

    Route::post('custom-integration-fields', [CustomIntegrationFieldController::class, 'store'])->name('custom-integration-fields.store');

    Route::get('pipedrive-sync', function () {
        PipedriveIntegrationService::syncAgentDeals();

        return back();
    })->name('pipedrive.sync');
});
