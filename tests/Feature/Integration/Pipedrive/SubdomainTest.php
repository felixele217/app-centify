<?php

use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;

it('stores the pipedrive subdomain for the organization upon syncing the data', function () {
    $admin = signInAdmin();

    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $agent = Agent::factory()->create([
        'email' => PipedriveHelper::demoSetByEmail($deals[0]),
        'organization_id' => $admin->organization->id,
    ]);

    $subdomain = PipedriveHelper::organizationSubdomain($deals[0]);

    (new PipedriveIntegrationService())->syncAgentDeals();

    // model pipedrive config mit den tokens, subdomain und was noch kommt
    expect($admin->organization->pipedrive_subdomain)->toBe($subdomain);
})->todo();
