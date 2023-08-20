<?php

use App\Enum\IntegrationTypeEnum;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\Integration;

it('does not throw an error on deals for agent if the agent has no active plan', function () {
    $admin = signInAdmin();

    Integration::factory()->create([
        'organization_id' => $admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    $pipedriveFacade = new PipedriveFacade($admin->organization);

    $deals = $pipedriveFacade->deals();

    $agent = Agent::factory()
        ->ofOrganization($admin->organization_id)
        ->create();

    $pipedriveIntegrationService = new PipedriveIntegrationService($admin->organization);

    expect($pipedriveIntegrationService->dealsForAgent($agent, $deals))->toHaveCount(0);
});

it('maps over all plans and not only the first active', function () {

})->todo();
