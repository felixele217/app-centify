<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;

it('returns all demo scheduled shareholders except the owner', function () {
    $trigger = TriggerEnum::DEMO_SET_BY;

    $deal = Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, $trigger)
        ->create();

    foreach ($shareholders = Agent::factory($shareholdersCount = 2)->create() as $agent) {
        AgentDeal::factory()->create([
            'deal_id' => $deal->id,
            'agent_id' => $agent->id,
            'triggered_by' => $trigger->value,
        ]);
    }

    expect($deal->demoScheduledShareholders)->toHaveCount($shareholdersCount);
    expect($deal->demoScheduledShareholders->contains($shareholders->first()))->toBeTrue();
    expect($deal->demoScheduledShareholders->contains($shareholders->last()))->toBeTrue();
});

it('returns all deal won shareholders except the owner', function () {
    $trigger = TriggerEnum::DEAL_WON;

    $deal = Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, $trigger)
        ->create();

    foreach ($shareholders = Agent::factory($shareholdersCount = 2)->create() as $agent) {
        AgentDeal::factory()->create([
            'deal_id' => $deal->id,
            'agent_id' => $agent->id,
            'triggered_by' => $trigger->value,
        ]);
    }

    expect($deal->dealWonShareholders)->toHaveCount($shareholdersCount);
    expect($deal->dealWonShareholders->contains($shareholders->first()))->toBeTrue();
    expect($deal->dealWonShareholders->contains($shareholders->last()))->toBeTrue();
});