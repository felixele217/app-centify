<?php

use App\Enum\CustomFieldEnum;
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
        ->has(Plan::factory()->active()->count(1)->state([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]))
        ->create([
            'email' => PipedriveHelper::ownerEmail($this->deals[3]), // deals[3] is a won deal
        ]);
});

it('auto-accepts deals if the flag is set in the organization', function () {
    $this->admin->organization->update(['auto_accept_deal_won' => true]);

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($this->agent->deals->first()->ae->pivot->accepted_at)->not()->toBeNull();
});

it('does not auto-accept deals if the flag is not set in the organization', function () {
    $this->admin->organization->update(['auto_accept_deal_won' => false]);

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($this->agent->deals->first()->ae->pivot->accepted_at)->toBeNull();
});
