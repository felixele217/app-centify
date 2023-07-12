<?php

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Helper\DateHelper;
use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\CustomIntegrationField;
use App\Models\Deal;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $admin = signInAdmin();

    CustomIntegrationField::create([
        'organization_id' => $admin->organization->id,
        'name' => CustomIntegrationFieldEnum::DEMO_SET_BY->value,
        'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
    ]);
});

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

    $agent = Agent::factory()->create([
        'email' => $email,
    ]);

    PipedriveIntegrationService::syncAgentDeals();

    $expectedData = PipedriveIntegrationService::agentDeals();

    expect($agent->deals)->toHaveCount(expectedDealCount($email, $deals));
    expect($agent->deals->first()->integration_deal_id)->toBe($expectedData[$email][0]['id']);
    expect($agent->deals->first()->title)->toBe($expectedData[$email][0]['title']);
    expect($agent->deals->first()->value)->toBe($expectedData[$email][0]['value']);
    expect($agent->deals->first()->status->value)->toBe($expectedData[$email][0]['status']);
    expect($agent->deals->first()->owner_email)->toBe($expectedData[$email][0]['owner_email']);
    expect($agent->deals->first()->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
    expect(DateHelper::parsePipedriveTime($expectedData[$email][0]['add_time'])->toDateTimeString())->toBe($agent->deals->first()->add_time->toDateTimeString());
});

it('updates the deal if it already existed and some data changed', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $agent = Agent::factory()->create([
        'email' => $deals[0]['creator_user_id']['email'],
    ]);

    PipedriveIntegrationService::syncAgentDeals();

    $agentDeal = $agent->fresh()->deals()->whereIntegrationDealId($deals[0]['id'])->first();

    $agentDeal->update([
        'value' => $deals[0]['value'] + 5,
    ]);

    PipedriveIntegrationService::syncAgentDeals();

    expect($agentDeal->fresh()->value)->toBe($deals[0]['value']);
});

it('does not create the same entry twice', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $email = $deals[0]['creator_user_id']['email'];

    $agent = Agent::factory()->create([
        'email' => $email,
    ]);

    PipedriveIntegrationService::syncAgentDeals();
    PipedriveIntegrationService::syncAgentDeals();

    expect($agent->deals)->toHaveCount(expectedDealCount($email, $deals));
});

it('does not create deal if no agent with the pipedrive email exists', function () {
    PipedriveIntegrationService::syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('does not create deal if demo_set_by has a value assigned to it', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $email = $deals[0]['creator_user_id']['email'];

    Agent::factory()->create([
        'email' => $email,
        'organization_id' => Auth::user()->organization->id,
    ]);

    PipedriveIntegrationService::syncAgentDeals();

    expect(Deal::count())->toBe(demoSetByCount($deals));
});

function expectedDealCount(string $email, array $deals): int
{
    return min(emailCount($email, $deals), demoSetByCount($deals));
}

function emailCount($email, $deals): int
{
    $emailCount = 0;

    foreach ($deals as $deal) {
        if ($deal['creator_user_id']['email'] === $email) {
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
