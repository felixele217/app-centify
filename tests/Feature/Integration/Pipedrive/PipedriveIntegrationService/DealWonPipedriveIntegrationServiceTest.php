<?php

use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Facades\PipedriveFacade;
use App\Helper\DateHelper;
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

// it('returns deals where demo set by is set in the correct format', function () {
//     $agentDeals = (new PipedriveIntegrationService($this->admin->organization))->agentDeals();

//     $firstDeal = $agentDeals[0][array_keys($agentDeals[0])[0]]->first();

//     expect($firstDeal)->toHaveKeys([
//         'id',
//         'title',
//         'value',
//         'add_time',
//         'status',
//         'owner_email',
//     ]);
// });

// it('stores the data properly', function () {
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     $expectedData = (new PipedriveIntegrationService($this->admin->organization))->agentDeals()[0][$this->agent->email][0];

//     expect($this->agent->deals)->toHaveCount(expectedDealCount($this->agent->email, $this->deals));
//     expect($this->agent->deals->first()->integration_deal_id)->toBe($expectedData['id']);
//     expect($this->agent->deals->first()->title)->toBe($expectedData['title']);
//     expect($this->agent->deals->first()->value)->toBe($expectedData['value']);
//     expect($this->agent->deals->first()->status->value)->toBe($expectedData['status']);
//     expect($this->agent->deals->first()->owner_email)->toBe($expectedData['owner_email']);
//     expect($this->agent->deals->first()->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
//     expect(DateHelper::parsePipedriveTime($expectedData['add_time'])->toDateTimeString())->toBe($this->agent->deals->first()->add_time->toDateTimeString());
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

//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect(Deal::count())->toBe(0);
// });

// it('does not create deal if demo_set_by has no value assigned to it', function () {
//     (new PipedriveIntegrationService($this->admin->organization))->syncAgentDeals();

//     expect(Deal::count())->toBe(demoSetByCount($this->deals));
// });

// function expectedDealCount(string $email, array $deals): int
// {
//     return min(emailCount($email, $deals), demoSetByCount($deals));
// }

// function emailCount($email, $deals): int
// {
//     $emailCount = 0;

//     foreach ($deals as $deal) {
//         if (PipedriveHelper::demoSetByEmail($deal, env('PIPEDRIVE_DEMO_SET_BY')) === $email) {
//             $emailCount++;
//         }
//     }

//     return $emailCount;
// }

// function demoSetByCount($deals): int
// {
//     $demoSetByCount = 0;

//     foreach ($deals as $deal) {
//         if ($deal[env('PIPEDRIVE_DEMO_SET_BY')] !== null) {
//             $demoSetByCount++;
//         }
//     }

//     return $demoSetByCount;
// }
