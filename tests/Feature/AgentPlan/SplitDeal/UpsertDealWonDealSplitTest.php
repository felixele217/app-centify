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
        ->create([
            'won_time' => Carbon::yesterday(),
        ]);
});

it('can store a split for a deal that is won', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_percentage' => $dealPercentage = 50,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_percentage' => $dealPercentage2 = 20,
            ],
        ],
    ]);

    expect($this->deal->ae->pivot->fresh()->deal_percentage)->toBe((100 - $dealPercentage - $dealPercentage2) / 100);
    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->deal_percentage)->toBe($dealPercentage / 100);
    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_percentage)->toBe($dealPercentage2 / 100);
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
                'deal_percentage' => $newSharedPercentage = 50,
                'id' => $existingAgentId,
            ],
            [
                'deal_percentage' => 20,
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
            ],
        ],
    ]);

    expect($this->deal->dealWonShareholders->count())->toBe(2);
    expect($agentDeal->fresh()->deal_percentage)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is not present in the request', function () {
    AgentDeal::factory()->create(['deal_id' => $this->deal->id, 'triggered_by' => TriggerEnum::DEAL_WON->value]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [],
    ]);

    expect(AgentDeal::count())->toBe(1);
});
