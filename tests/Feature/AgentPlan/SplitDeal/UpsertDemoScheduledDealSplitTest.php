<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();
});

it('can store a split for a deal that has a scheduled demo', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => $dealPercentage = 50,
                'deal_won_deal_percentage' => 0,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => $dealPercentage2 = 20,
                'deal_won_deal_percentage' => 0,
            ],
        ],
    ]);

    expect($this->deal->fresh()->sdr->pivot->deal_factor)->toBe((100 - $dealPercentage - $dealPercentage2) / 100);
    expect($this->deal->fresh()->sdr->pivot->agent_id)->not()->toBe($agentId);
    expect($this->deal->fresh()->sdr->pivot->agent_id)->not()->toBe($agentId2);

    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->deal_factor)->toBe($dealPercentage / 100);
    expect($this->deal->agents()->whereAgentId($agentId)->first()->pivot->triggered_by->value)->toBe(TriggerEnum::DEMO_SCHEDULED->value);

    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->deal_factor)->toBe($dealPercentage2 / 100);
    expect($this->deal->agents()->whereAgentId($agentId2)->first()->pivot->triggered_by->value)->toBe(TriggerEnum::DEMO_SCHEDULED->value);
});

it('updates the splits correctly if there already were some', function () {
    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create();

    $sdrAgentDeal = AgentDeal::factory()->create([
        'deal_id' => $deal->id,
        'agent_id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
        'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
        'deal_percentage' => 100,
    ]);

    $this->put(route('deals.splits.upsert', $deal), [
        'partners' => [
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => $newSharedPercentage = 50,
                'deal_won_deal_percentage' => 0,
            ],
        ],
    ]);

    expect($deal->fresh()->demoScheduledShareholders->first()->pivot->deal_percentage)->toBe($newSharedPercentage);
    expect($deal->SDR->pivot->deal_percentage)->toBe(100 - $newSharedPercentage);
});

it('updates the deal percentage correctly if the deal is already won', function () {
    $agentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $existingAgentId = Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
        'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
        'deal_percentage' => 10,
    ]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => $existingAgentId,
                'demo_scheduled_deal_percentage' => $newSharedPercentage = 50,
                'deal_won_deal_percentage' => 0,
            ],
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'demo_scheduled_deal_percentage' => 20,
                'deal_won_deal_percentage' => 0,
            ],
        ],
    ]);

    expect($this->deal->demoScheduledShareholders->count())->toBe(2);
    expect($agentDeal->fresh()->deal_factor)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is set to 0', function () {
    $dealWonAgentDeal = AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    AgentDeal::factory()->create([
        'deal_id' => $this->deal->id,
        'agent_id' => $dealWonAgentDeal->agent_id,
        'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'demo_scheduled_deal_percentage' => 0,
                'deal_won_deal_percentage' => 20,
                'id' => $dealWonAgentDeal->agent_id,
            ],
        ],
    ]);

    expect(AgentDeal::count())->toBe(2);
});
