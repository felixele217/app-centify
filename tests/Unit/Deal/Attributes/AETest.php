<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;

it('AE is computed correctly', function () {
    $deal = Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEAL_WON)
        ->create();

    AgentDeal::factory()->create([
        'created_at' => AgentDeal::whereAgentId($agentId)->whereDealId($deal->id)->first()->created_at->subDays(2),
        'agent_id' => $agentId2 = Agent::factory()->create()->id,
        'deal_id' => $deal->id,
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    expect($deal->AE->id)->toBe($agentId2);
});

it('AE does not return SDR', function () {
    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    expect($deal->AE)->toBeNull();
});

it('AE does not return split deal partners', function () {
    $deal = Deal::factory()->create();

    AgentDeal::factory()->create([
        'agent_id' => Agent::factory()->create()->id,
        'deal_id' => $deal->id,
        'deal_percentage' => 50,
    ]);

    expect($deal->AE)->toBeNull();
});