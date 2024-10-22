<?php

use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\IntegrationCustomFieldController;
use App\Http\Controllers\PipedriveAuthController;
use App\Http\Controllers\SalesforceAuthController;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Route::get('salesforce-auth', [SalesforceAuthController::class, 'create'])->name('authenticate.salesforce.create');
    // Route::get('salesforce-callback', [SalesforceAuthController::class, 'store'])->name('authenticate.salesforce.store');
    // Route::get('salesforce-test', function () {
    //     return back()->withErrors([
    //         'implementation' => 'not yet implemented',
    //     ]);
    // })->name('salesforce.test');

    Route::get('pipedrive-auth', [PipedriveAuthController::class, 'create'])->name('authenticate.pipedrive.create');
    Route::get('pipedrive-callback', [PipedriveAuthController::class, 'store'])->name('authenticate.pipedrive.store');

    Route::get('/integrations', IntegrationController::class)->name('integrations.index');

    Route::prefix('integrations/{integration}/custom-fields')->name('integrations.custom-fields.')->controller(IntegrationCustomFieldController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{customField}', 'update')->name('update');
    });

    Route::get('pipedrive-sync', function () {
        (new PipedriveIntegrationService(Auth::user()->organization))->syncAgentDeals();

        return redirect(request()->query('redirect_url') ?? route('integrations.index'));
    })->name('pipedrive.sync');
});
