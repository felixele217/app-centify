<?php

use App\Exceptions\SyncWithoutConnectionException;
use App\Integrations\Pipedrive\PipedriveIntegrationService;

it('throws an exception if trying to sync without an active connection', function () {
    $admin = signInAdmin();

    (new PipedriveIntegrationService($admin->organization))->syncAgentDeals();
})->throws(SyncWithoutConnectionException::class);
