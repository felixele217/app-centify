<?php

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveDealDTO;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Deal;
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
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]))
        ->create([
            'email' => PipedriveHelper::demoSetByEmail($this->deals[0], env('PIPEDRIVE_DEMO_SET_BY')),
        ]);

    $this->agent2 = Agent::factory()
        ->ofOrganization($this->admin->organization_id)
        ->has(Plan::factory()->active()->count(1)->state([
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]))
        ->create([
            'email' => PipedriveHelper::demoSetByEmail($this->deals[3], env('PIPEDRIVE_DEMO_SET_BY')),
        ]);
});

it('returns deals in the correct format', function () {
    $dealDTO = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email][0];

    expect(get_class($dealDTO))->toBe(PipedriveDealDTO::class);
});

it('returns no deals if agent has no active plan', function () {
    $this->agent->plans()->active()->first()->delete();
    $this->agent2->plans()->active()->first()->delete();
    $dealsAgent1 = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email];
    $dealsAgent2 = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent2->email];

    expect(count($dealsAgent1))->toBe(0);
    expect(count($dealsAgent2))->toBe(0);
});

it('stores the data properly', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    $expectedDealDTO = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email][0];

    expect($this->agent->deals)->toHaveCount(expectedDealCount($this->agent->email, $this->deals));
    expect($this->agent->deals->first()->integration_deal_id)->toBe($expectedDealDTO->integration_deal_id);
    expect($this->agent->deals->first()->title)->toBe($expectedDealDTO->title);
    expect($this->agent->deals->first()->value)->toBe($expectedDealDTO->value);
    expect($this->agent->deals->first()->status->value)->toBe($expectedDealDTO->status->value);
    expect($this->agent->deals->first()->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
    expect($this->agent->deals->first()->add_time->toDateTimeString())->toBe($expectedDealDTO->add_time->toDateTimeString());
});

it('updates the deal if it already existed and some data changed', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    $agentDeal = $this->agent->fresh()->deals()->whereIntegrationDealId($this->deals[0]['id'])->first();

    $agentDeal->update([
        'value' => $this->deals[0]['value'] + 5,
    ]);

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($agentDeal->fresh()->value)->toBe($this->deals[0]['value']);
});

it('does not create the same entry twice', function () {
    $email = PipedriveHelper::demoSetByEmail($this->deals[0], env('PIPEDRIVE_DEMO_SET_BY'));

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($this->agent->deals)->toHaveCount(expectedDealCount($email, $this->deals));
});

it('does not create deal if no agent with the pipedrive email exists', function () {
    $this->agent->delete();
    $this->agent2->delete();

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('does not create deal if demo_set_by has no value assigned to it', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect(Deal::count())->toBe(demoSetByCount($this->deals));
});

function expectedDealCount(string $email, array $deals): int
{
    return min(emailCount($email, $deals), demoSetByCount($deals));
}

function emailCount($email, $deals): int
{
    $emailCount = 0;

    foreach ($deals as $deal) {
        if (PipedriveHelper::demoSetByEmail($deal, env('PIPEDRIVE_DEMO_SET_BY')) === $email) {
            $emailCount++;
        }
    }

    return $emailCount;
}

function demoSetByCount($deals): int
{
    $demoSetByCount = 0;

    foreach ($deals as $deal) {
        if ($deal[env('PIPEDRIVE_DEMO_SET_BY')] !== null) {
            $demoSetByCount++;
        }
    }

    return $demoSetByCount;
}
