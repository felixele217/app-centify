<?php

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Deal;
use App\Models\Integration;
use App\Models\Plan;
use Carbon\Carbon;
use Tests\Feature\Integration\Pipedrive\PipedriveIntegrationService\Helper\AssertionHelper;

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

it('does not create deal if no agent with the corresponding pipedrive email exists', function () {
    $this->agent->delete();

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('does not throw an error on deals for agent if the agent has no active plan', function () {
    $pipedriveIntegrationService = new PipedriveIntegrationService($this->admin->organization);

    expect(count($pipedriveIntegrationService->agentDeals($this->deals)))->toBe(0);
});

it('only syncs deals whose add time is after the plan start date', function () {
    $this->agent->plans()->attach(Plan::factory()->create([
        'start_date' => Carbon::yesterday(),
    ]));

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('maps over all active plans and syncs all deals where one of the triggers is achieved', function () {
    $this->agent->plans()->attach([
        Plan::factory()->active()->create([
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ])->id,
        Plan::factory()->active()->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ])->id,
    ]);

    $deals = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email];

    expect(count($deals))->toBe(AssertionHelper::dealsCountForAllTriggers($this->agent->email, $this->deals));
});
