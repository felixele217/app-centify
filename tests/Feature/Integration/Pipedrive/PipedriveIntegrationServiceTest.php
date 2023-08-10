<?php

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Helper\DateHelper;
use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Deal;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->integration = Integration::factory()->create([
        'organization_id' => $this->admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    CustomField::create([
        'name' => CustomFieldEnum::DEMO_SET_BY->value,
        'integration_id' => $this->integration->id,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
    ]);
});

it('returns the correct structure for agentDeals', function () {
    $agentDeals = (new PipedriveIntegrationService($this->integration))->agentDeals();

    $firstDeal = $agentDeals[0][array_keys($agentDeals[0])[0]]->first();

    expect($firstDeal)->toHaveKeys([
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

    $email = PipedriveHelper::demoSetByEmail($deals[0]);

    $agent = Agent::factory()->create([
        'email' => $email,
        'organization_id' => $this->admin->organization->id,
    ]);

    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

    $expectedData = (new PipedriveIntegrationService($this->integration))->agentDeals()[0][$email][0];

    expect($agent->deals)->toHaveCount(expectedDealCount($email, $deals));
    expect($agent->deals->first()->integration_deal_id)->toBe($expectedData['id']);
    expect($agent->deals->first()->title)->toBe($expectedData['title']);
    expect($agent->deals->first()->value)->toBe($expectedData['value']);
    expect($agent->deals->first()->status->value)->toBe($expectedData['status']);
    expect($agent->deals->first()->owner_email)->toBe($expectedData['owner_email']);
    expect($agent->deals->first()->integration_type->value)->toBe(IntegrationTypeEnum::PIPEDRIVE->value);
    expect(DateHelper::parsePipedriveTime($expectedData['add_time'])->toDateTimeString())->toBe($agent->deals->first()->add_time->toDateTimeString());
});

it('updates the deal if it already existed and some data changed', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $agent = Agent::factory()->create([
        'email' => PipedriveHelper::demoSetByEmail($deals[0]),
        'organization_id' => $this->admin->organization->id,
    ]);

    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

    $agentDeal = $agent->fresh()->deals()->whereIntegrationDealId($deals[0]['id'])->first();

    $agentDeal->update([
        'value' => $deals[0]['value'] + 5,
    ]);

    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

    expect($agentDeal->fresh()->value)->toBe($deals[0]['value']);
});

it('does not create the same entry twice', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $email = PipedriveHelper::demoSetByEmail($deals[0]);

    $agent = Agent::factory()->create([
        'email' => PipedriveHelper::demoSetByEmail($deals[0]),
        'organization_id' => $this->admin->organization->id,
    ]);

    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();
    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

    expect($agent->deals)->toHaveCount(expectedDealCount($email, $deals));
});

it('does not create deal if no agent with the pipedrive email exists', function () {
    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

    expect(Deal::count())->toBe(0);
});

it('does not create deal if demo_set_by has a value assigned to it', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    $email = PipedriveHelper::demoSetByEmail($deals[0]);

    Agent::factory()->create([
        'email' => $email,
        'organization_id' => Auth::user()->organization->id,
    ]);

    (new PipedriveIntegrationService($this->integration))->syncAgentDeals();

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
        if (PipedriveHelper::demoSetByEmail($deal) === $email) {
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
