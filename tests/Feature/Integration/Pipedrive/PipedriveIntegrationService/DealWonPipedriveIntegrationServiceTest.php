<?php

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveDealDTO;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Integration;
use App\Models\Plan;
use Tests\Feature\Integration\Pipedrive\PipedriveIntegrationService\Helper\AssertionHelper;

beforeEach(function () {
    $this->admin = signInAdmin();

    Integration::factory()->create([
        'organization_id' => $this->admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    $this->pipedriveFacade = new PipedriveFacade($this->admin->organization);

    $this->deals = $this->pipedriveFacade->deals();

    $this->agent = Agent::factory()
        ->ofOrganization($this->admin->organization_id)
        ->has(Plan::factory()->active()->count(1)->state([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]))
        ->create([
            'email' => PipedriveHelper::ownerEmail($this->deals[3]), // deals[3] is a won deal
        ]);
});

it('returns deals in the correct format', function () {
    $dealDTO = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email]->first();

    expect(get_class($dealDTO))->toBe(PipedriveDealDTO::class);
});

it('stores the data properly', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    $expectedDealDTO = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email]->first();

    foreach ($this->agent->deals as $deal) {
        expect($deal->AE->id)->toBe($this->agent->id);
    }

    $agentDeal = $this->agent->deals()->whereIntegrationDealId($expectedDealDTO->integration_deal_id)->first();
    expect($this->agent->deals)->toHaveCount(AssertionHelper::dealsCountForTrigger($this->agent->email, $this->deals, TriggerEnum::DEAL_WON));
    expect($agentDeal->integration_deal_id)->toBe($expectedDealDTO->integration_deal_id);
    expect($agentDeal->title)->toBe($expectedDealDTO->title);
    expect(floatval($agentDeal->value))->toBe($expectedDealDTO->value);
    expect($agentDeal->status->value)->toBe($expectedDealDTO->status->value);
    expect($agentDeal->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
    expect($agentDeal->add_time->toDateTimeString())->toBe($expectedDealDTO->add_time->toDateTimeString());

    if ($won_time = $agentDeal->won_time) {
        expect($won_time->toDateTimeString())->toBe($expectedDealDTO->won_time->toDateTimeString());
    } else {
        expect($expectedDealDTO->won_time)->toBeNull();
    }
});

it('updates the deal if it already existed and some data changed', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    $agentDeal = $this->agent->fresh()->deals()->whereIntegrationDealId($this->deals[3]['id'])->first();

    $agentDeal->update([
        'value' => $this->deals[0]['value'] + 5,
    ]);

    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($agentDeal->fresh()->value)->toBe($this->deals[0]['value']);
});

it('does not create the same entry twice', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    expect($this->agent->deals)->toHaveCount(AssertionHelper::dealsCountForTrigger($this->agent->email, $this->deals, TriggerEnum::DEAL_WON));
});

it('does not create deal if status is not won', function () {
    (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

    $allDealsWhereStatusIsWonCount = 0;

    foreach ($this->deals as $deal) {
        if ($deal['status'] === DealStatusEnum::WON->value) {
            $allDealsWhereStatusIsWonCount++;
        }
    }

    expect(Deal::count())->toBe($allDealsWhereStatusIsWonCount);
});
