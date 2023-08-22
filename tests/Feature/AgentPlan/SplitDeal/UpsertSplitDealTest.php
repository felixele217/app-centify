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

    $this->sdrAgentDeal = $this->deal->sdr->pivot;
});

it('can store a split for a deal that is not won yet', function () {
    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage = 50,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage2 = 20,
            ],
        ],
    ]);

    expect($this->sdrAgentDeal->fresh()->deal_percentage)->toBe((100 - $sharedPercentage - $sharedPercentage2) / 100);
    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->deal_percentage)->toBe($sharedPercentage / 100);
    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_percentage)->toBe($sharedPercentage2 / 100);
});

it('can store a split for a deal that is already won', function () {
    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEAL_WON)
        ->create([
            'won_time' => Carbon::yesterday(),
        ]);

    $this->post(route('deals.splits.store', $deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage = 50,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage2 = 20,
            ],
        ],
    ]);

    expect($deal->ae->pivot->fresh()->deal_percentage)->toBe((100 - $sharedPercentage - $sharedPercentage2) / 100);
    expect($deal->agents()->whereAgentId($agentId)->first()->pivot->deal_percentage)->toBe($sharedPercentage / 100);
    expect($deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_percentage)->toBe($sharedPercentage2 / 100);
});

it('updates the splits correctly if there already were some', function () {
    $agentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $existingAgentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
        'deal_percentage' => 10,
    ]);

    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'shared_percentage' => $newSharedPercentage = 50,
                'id' => $existingAgentId,
            ],
            [
                'shared_percentage' => 20,
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
            ],
        ],
    ]);

    expect($this->deal->fresh()->agents()->wherePivotNull('triggered_by')->count())->toBe(2);
    expect($agentDeal->fresh()->deal_percentage)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is not present in the request', function () {
    AgentDeal::factory()->create(['deal_id' => $this->deal->id]);

    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [],
    ]);

    expect(AgentDeal::whereNull('triggered_by')->count())->toBe(0);
});

it('cannot store a split with a percentage_share of 0', function () {
    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'shared_percentage' => 0,
            ],
        ],
    ])->assertInvalid([
        'partners.0.shared_percentage' => "The partners' shared percentage must be greater than 0.",
    ]);
});

it('returns correct validation error messages', function () {
    $this->post(route('deals.splits.store', $this->deal), [
        'partners' => [
            [
                'id' => null,
                'shared_percentage' => null,
            ],
        ],
    ])->assertInvalid([
        'partners.0.id' => "The partners' identifier field is required.",
        'partners.0.shared_percentage' => "The partners' shared percentage field is required.",
    ]);
});
