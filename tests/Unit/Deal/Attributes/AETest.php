<?php

use App\Models\Deal;
use App\Models\Agent;
use App\Enum\TriggerEnum;
use App\Models\AgentDeal;

it('AE is computed correctly', function () {
    $deal = Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEAL_WON)
        ->create();

    expect($deal->AE->id)->toBe($agentId);
});

it('AE does not return SDR', function () {
    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    expect($deal->AE)->toBeNull();
});

it('AE does not return split deal partners', function () {
    $deal = Deal::factory() ->create();

    AgentDeal::factory()->create([
        'agent_id' => Agent::factory()->create()->id,
        'deal_id' => $deal->id,
        'deal_percentage' => 50,
    ]);

    expect($deal->AE)->toBeNull();
});
