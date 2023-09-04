<?php

use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;

it('returns the correct deal percentage for an agent', function () {
    AgentDeal::factory()->create([
        'agent_id' => $agent = Agent::factory()->create(),
        'deal_id' => $deal = Deal::factory()->create(),
        'deal_percentage' => $dealPercentage = 50,
    ]);

    expect($deal->percentageFactorForAgent($agent))->toBe($dealPercentage / 100);
});
