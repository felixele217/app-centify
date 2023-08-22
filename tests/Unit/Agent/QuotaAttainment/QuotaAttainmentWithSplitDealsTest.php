<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly for splitted deals', function (int $sharedPercentage1, ?int $sharedPercentage2) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->create());

    AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $sharedPercentage1 - $sharedPercentage2,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
        'deal_percentage' => $sharedPercentage1,
        'deal_id' => $agent->deals()->wherePivotNotNull('triggered_by')->first()->id,
    ]);

    if ($sharedPercentage2) {
        AgentDeal::create([
            'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
            'deal_percentage' => $sharedPercentage2,
            'deal_id' => $agent->deals()->wherePivotNotNull('triggered_by')->first()->id,
        ]);
    }

    expect((new QuotaAttainmentService($agent, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval((100 - $sharedPercentage1 - $sharedPercentage2) / 100));
})->with([
    [20, 30],
    [50, 40],
    [10, 5],
    [50, null],
    [50, 50],
]);

it('split partner also get the quota', function (int $sharedPercentage) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($splitPartner = Agent::factory()->create());
    $plan->agents()->attach($agent = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $sharedPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $sharedPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
    ]);

    expect((new QuotaAttainmentService($splitPartner, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval($sharedPercentage / 100));
})->with([
    20,
    40,
    60,
    80,
    100,
]);

it('deal owner gets his capped share', function (int $sharedPercentage) {
    $plan = Plan::factory()
        ->hasCap([
            'value' => $cap = 5_000_00,
        ])
        ->create([
            'target_amount_per_month' => 10_000_00,
        ]);

    $plan->agents()->attach($agent = Agent::factory()->create());
    $plan->agents()->attach($splitPartner = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $sharedPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 10_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $sharedPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
    ]);

    expect((new QuotaAttainmentService($agent, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval(min(0.5, (100 - $sharedPercentage) / 100)));
})->with([
    20,
    40,
    60,
    80,
    100,
]);

it('split partner gets his capped share', function (int $sharedPercentage) {
    $plan = Plan::factory()
        ->hasCap([
            'value' => $cap = 5_000_00,
        ])
        ->create([
            'target_amount_per_month' => 10_000_00,
        ]);

    $plan->agents()->attach($agent = Agent::factory()->create());
    $plan->agents()->attach($splitPartner = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $sharedPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 10_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $sharedPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
    ]);

    expect((new QuotaAttainmentService($splitPartner, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval(min(0.5, $sharedPercentage / 100)));
})->with([
    20,
    40,
    60,
    80,
    100,
]);
