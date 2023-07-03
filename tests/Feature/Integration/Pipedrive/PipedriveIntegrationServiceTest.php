<?php

use App\Integrations\Pipedrive\PipedriveIntegrationService;

it('returns the correct structure for agentDeals', function () {
    $agentDeals = PipedriveIntegrationService::agentDeals();

    expect($agentDeals[array_keys($agentDeals)[0]]->first())->toHaveKeys([
        'id',
        'title',
        'target_amount',
        'add_time',
        'status',
    ]);
});

it('returns no duplicates for duplicate emails', function () {

})->todo();
