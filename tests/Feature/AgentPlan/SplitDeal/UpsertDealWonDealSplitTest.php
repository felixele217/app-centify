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
        ->won(Carbon::yesterday())
        ->create();
});

it('can store a split for a deal that is won', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_won_deal_percentage' => $dealPercentage = 50,
                'demo_scheduled_deal_percentage' => 0,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_won_deal_percentage' => $dealPercentage2 = 20,
                'demo_scheduled_deal_percentage' => 0,
            ],
        ],
    ]);

    expect($this->deal->ae->pivot->fresh()->deal_factor)->toBe((100 - $dealPercentage - $dealPercentage2) / 100);
    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->deal_factor)->toBe($dealPercentage / 100);
    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_factor)->toBe($dealPercentage2 / 100);
});

it('updates the splits correctly if there already were some', function () {
    $agentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $existingAgentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
        'triggered_by' => TriggerEnum::DEAL_WON->value,
        'deal_percentage' => 10,
    ]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $existingAgentId,
                'deal_won_deal_percentage' => $newSharedPercentage = 50,
                'demo_scheduled_deal_percentage' => 0,
            ],
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_won_deal_percentage' => 20,
                'demo_scheduled_deal_percentage' => 0,
            ],
        ],
    ]);

    expect($this->deal->dealWonShareholders->count())->toBe(2);
    expect($agentDeal->fresh()->deal_factor)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is set to 0', function () {
    $demoScheduledAgentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);
    AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $demoScheduledAgentDeal->agent_id,
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'demo_scheduled_deal_percentage' => 20,
                'deal_won_deal_percentage' => 0,
                'id' => $demoScheduledAgentDeal->agent_id,
            ],
        ],
    ]);

    expect(AgentDeal::count())->toBe(2);
});
