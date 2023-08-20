<?php

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

// it('stores the data properly', function () {
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     $expectedDealDTO = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[$this->agent->email]->first();

//     expect($this->agent->deals)->toHaveCount(expectedDealCount($this->agent->email, $this->deals));
//     expect($this->agent->deals->first()->integration_deal_id)->toBe($expectedDealDTO->integration_deal_id);
//     expect($this->agent->deals->first()->title)->toBe($expectedDealDTO->title);
//     expect($this->agent->deals->first()->value)->toBe($expectedDealDTO->value);
//     expect($this->agent->deals->first()->status->value)->toBe($expectedDealDTO->status->value);
//     expect($this->agent->deals->first()->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
//     expect($this->agent->deals->first()->add_time->toDateTimeString())->toBe($expectedDealDTO->add_time->toDateTimeString());
// });

// it('updates the deal if it already existed and some data changed', function () {
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     $agentDeal = $this->agent->fresh()->deals()->whereIntegrationDealId($this->deals[0]['id'])->first();

//     $agentDeal->update([
//         'value' => $this->deals[0]['value'] + 5,
//     ]);

//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect($agentDeal->fresh()->value)->toBe($this->deals[0]['value']);
// });

// it('does not create the same entry twice', function () {
//     $email = PipedriveHelper::demoSetByEmail($this->deals[0], env('PIPEDRIVE_DEMO_SET_BY'));

//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect($this->agent->deals)->toHaveCount(expectedDealCount($email, $this->deals));
// });

// it('does not create deal if no agent with the pipedrive email exists', function () {
//     $this->agent->delete();
//     $this->agent2->delete();

//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect(Deal::count())->toBe(0);
// });

// it('does not create deal if demo_set_by has no value assigned to it', function () {
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect(Deal::count())->toBe(demoSetByCount($this->deals));
// });
