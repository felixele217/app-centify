<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEMO_SET_BY)
        ->create();
});

it('can store a split for a deal that is only scheduled', function () {
    $this->post(route('deals.splits.store', $this->deal), [
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

    expect($this->deal->fresh()->sdr->pivot->deal_percentage)->toBe((100 - $dealPercentage - $dealPercentage2) / 100);
    expect($this->deal->fresh()->sdr->pivot->id)->not()->toBe($agentId);
    expect($this->deal->fresh()->sdr->pivot->id)->not()->toBe($agentId2);

    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->deal_percentage)->toBe($dealPercentage / 100);
    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->triggered_by->value)->toBe(TriggerEnum::DEMO_SET_BY->value);

    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_percentage)->toBe($dealPercentage2 / 100);
    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->triggered_by->value)->toBe(TriggerEnum::DEMO_SET_BY->value);
});

it('can store a split for a deal that is won', function () {
    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEAL_WON)
        ->create([
            'won_time' => Carbon::yesterday(),
        ]);

    $this->post(route('deals.splits.store', $deal), [
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

    expect($deal->ae->pivot->fresh()->deal_percentage)->toBe((100 - $dealPercentage - $dealPercentage2) / 100);
    expect($deal->agents()->whereAgentId($agentId)->first()->pivot->deal_percentage)->toBe($dealPercentage / 100);
    expect($deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_percentage)->toBe($dealPercentage2 / 100);
});

it('updates the splits correctly if there already were some', function () {
    $agentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $existingAgentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'deal_percentage' => 10,
    ]);

    $this->post(route('deals.splits.store', $this->deal), [
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

    expect($this->deal->demoScheduledShareholders->count())->toBe(2);
    expect($agentDeal->fresh()->deal_percentage)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is not present in the request', function () {
    AgentDeal::factory()->create(['deal_id' => $this->deal->id, 'triggered_by' => TriggerEnum::DEMO_SET_BY->value]);

    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [],
    ]);

    expect(AgentDeal::count())->toBe(1);
});

it('cannot store a split with a percentage_share of 0', function () {
    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_percentage' => 0,
            ],
        ],
    ])->assertInvalid([
        'partners.0.deal_percentage' => "The partners' shared percentage must be greater than 0.",
    ]);
});

it('returns correct validation error messages', function () {
    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'id' => null,
                'deal_percentage' => null,
            ],
        ],
    ])->assertInvalid([
        'partners.0.id' => "The partners' identifier field is required.",
        'partners.0.deal_percentage' => "The partners' shared percentage field is required.",
    ]);
});