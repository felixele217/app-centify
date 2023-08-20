<?php

use App\Enum\CustomFieldEnum;
use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Integration;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();

    $integration = Integration::factory()->create([
        'organization_id' => $this->admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    CustomField::create([
        'name' => CustomFieldEnum::DEMO_SET_BY->value,
        'integration_id' => $integration->id,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
    ]);

    $this->pipedriveClient = new PipedriveFacade($this->admin->organization);

    $this->deals = $this->pipedriveClient->deals();

    $this->agent = Agent::factory()
        ->ofOrganization($this->admin->organization_id)
        ->create([
            'email' => PipedriveHelper::ownerEmail($this->deals[3]),
        ]);
});

it('does not throw an error on deals for agent if the agent has no active plan', function () {
    $pipedriveIntegrationService = new PipedriveIntegrationService($this->admin->organization);

    expect(count($pipedriveIntegrationService->agentDeals($this->deals)))->toBe(0);
});

it('maps over all plans and not only the first active', function () {
    $this->agent->plans()->attach([
        Plan::factory()->active()->create([
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ])->id,
        Plan::factory()->active()->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ])->id,
    ]);

    $deals = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email];

    expect(count($deals))->toBe(dealsCount($this->agent->email, $this->deals));
});

function dealsCount(string $email, array $deals): int
{
    $dealsCount = 0;

    foreach ($deals as $deal) {
        if (PipedriveHelper::demoSetByEmail($deal, env('PIPEDRIVE_DEMO_SET_BY')) === $email
        || (PipedriveHelper::ownerEmail($deal) === $email && $deal['status'] === DealStatusEnum::WON->value)) {
            $dealsCount++;
        }
    }

    return $dealsCount;
}
