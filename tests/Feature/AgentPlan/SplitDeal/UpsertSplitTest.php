<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEAL_WON)
        ->won(Carbon::now())
        ->create();

    $sdrAgentDeal = AgentDeal::factory()->create(['deal_id' => $this->deal->id, 'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value]);
});

it('can store a complex split scenario correctly', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => $agent1DemoScheduledPercentage = 30,
                'deal_won_deal_percentage' => $agent1DealWonPercentage = 40,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => $agent2DemoScheduledPercentage = 50,
                'deal_won_deal_percentage' => $agent2DealWonPercentage = 20,
            ],
        ],
    ]);

    expect($this->deal->sdr->pivot->fresh()->deal_factor)->toBe((100 - $agent1DemoScheduledPercentage - $agent2DemoScheduledPercentage) / 100);
    expect($this->deal->ae->pivot->fresh()->deal_factor)->toBe((100 - $agent1DealWonPercentage - $agent2DealWonPercentage) / 100);

    expect($this->deal->fresh()->demoScheduledShareholders->first()->pivot->deal_factor)->toBe($agent1DemoScheduledPercentage / 100);
    expect($this->deal->fresh()->demoScheduledShareholders->last()->pivot->deal_factor)->toBe($agent2DemoScheduledPercentage/ 100);

    expect($this->deal->fresh()->dealWonShareholders->first()->pivot->deal_factor)->toBe($agent1DealWonPercentage / 100);
    expect($this->deal->fresh()->dealWonShareholders->last()->pivot->deal_factor)->toBe($agent2DealWonPercentage / 100);
});

it('removes the split if it is not present in the request', function () {
    AgentDeal::factory()->create(['deal_id' => $this->deal->id, 'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value]);
    AgentDeal::factory()->create(['deal_id' => $this->deal->id, 'triggered_by' => TriggerEnum::DEAL_WON->value]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [],
    ]);

    expect(AgentDeal::count())->toBe(2);
});

it('returns correct validation error messages', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => null,
                'demo_scheduled_deal_percentage' => null,
                'deal_won_deal_percentage' => null,
            ],
        ],
    ])->assertInvalid([
        'partners.0.id' => "The partners' identifier field is required.",
    ]);
});
