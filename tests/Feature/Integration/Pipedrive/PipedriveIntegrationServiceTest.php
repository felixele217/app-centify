<?php

use App\Enum\IntegrationEnum;
use App\Helper\DateHelper;
use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\Deal;

it('returns the correct structure for agentDeals', function () {
    $agentDeals = PipedriveIntegrationService::agentDeals();

    expect($agentDeals[array_keys($agentDeals)[0]]->first())->toHaveKeys([
        'id',
        'title',
        'value',
        'add_time',
        'status',
        'owner_email',
    ]);
});

it('stores the data properly', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $email = $deals[0]['creator_user_id']['email'];
    $emailCount = 0;

    foreach ($deals as $deal) {
        if ($deal['creator_user_id']['email'] === $email) {
            $emailCount++;
        }
    }

    $agent = Agent::factory()->create([
        'email' => $email,
    ]);

    PipedriveIntegrationService::syncAgentDeals();

    $expectedData = PipedriveIntegrationService::agentDeals();

    expect($agent->deals)->toHaveCount($emailCount);
    expect($agent->deals->first()->integration_deal_id)->toBe($expectedData[$email][0]['id']);
    expect($agent->deals->first()->title)->toBe($expectedData[$email][0]['title']);
    expect($agent->deals->first()->value)->toBe($expectedData[$email][0]['value']);
    expect($agent->deals->first()->status->value)->toBe($expectedData[$email][0]['status']);
    expect($agent->deals->first()->owner_email)->toBe($expectedData[$email][0]['owner_email']);
    expect($agent->deals->first()->integration_type->value)->toBe(IntegrationEnum::PIPEDRIVE->value);
    expect(DateHelper::parsePipedriveTime($expectedData[$email][0]['add_time'])->toDateTimeString())->toBe($agent->deals->first()->add_time->toDateTimeString());
});

it('does not create deal / throw error if no agent with the pipedrive email exists', function () {
    PipedriveIntegrationService::syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('returns no duplicates for duplicate emails', function () {

})->todo();
