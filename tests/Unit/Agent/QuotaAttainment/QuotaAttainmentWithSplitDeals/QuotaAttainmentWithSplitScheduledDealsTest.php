<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\TotalQuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly for splitted deals', function (int $sharedPercentage1, ?int $sharedPercentage2) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
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
        'deal_id' => $agent->deals()->first()->SDR->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    if ($sharedPercentage2) {
        AgentDeal::create([
            'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
            'deal_percentage' => $sharedPercentage2,
            'deal_id' => $agent->deals()->first()->SDR->id,
            'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        ]);
    }

    expect((new TotalQuotaAttainmentService($agent, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval((100 - $sharedPercentage1 - $sharedPercentage2) / 100));
})->with([
    [20, 30],
    [50, 40],
    [10, 5],
    [50, null],
    [50, 50],
]);

it('split partner also get the quota', function (int $dealPercentage) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    $plan->agents()->attach($splitPartner = Agent::factory()->create());
    $plan->agents()->attach($agent = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $dealPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $dealPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    expect((new TotalQuotaAttainmentService($splitPartner, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval($dealPercentage / 100));
})->with([
    20,
    40,
    60,
    80,
    100,
]);

it('deal owner gets his capped share', function (int $dealPercentage) {
    $plan = Plan::factory()
        ->hasCap([
            'value' => $cap = 5_000_00,
        ])
        ->create([
            'target_amount_per_month' => 10_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]);

    $plan->agents()->attach($agent = Agent::factory()->create());
    $plan->agents()->attach($splitPartner = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $dealPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 10_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $dealPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    expect((new TotalQuotaAttainmentService($agent, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval(min(0.5, (100 - $dealPercentage) / 100)));
})->with([
    20,
    40,
    60,
    80,
    100,
]);

it('split partner gets his capped share', function (int $dealPercentage) {
    $plan = Plan::factory()
        ->hasCap([
            'value' => $cap = 5_000_00,
        ])
        ->create([
            'target_amount_per_month' => 10_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]);

    $plan->agents()->attach($agent = Agent::factory()->create());
    $plan->agents()->attach($splitPartner = Agent::factory()->create());

    $sdrAgentDeal = AgentDeal::create([
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'deal_percentage' => 100 - $dealPercentage,
        'deal_id' => Deal::factory()->state([
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 10_000_00,
        ])->create()->id,
    ]);

    AgentDeal::create([
        'agent_id' => $splitPartner->id,
        'deal_percentage' => $dealPercentage,
        'deal_id' => $sdrAgentDeal->deal->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    expect((new TotalQuotaAttainmentService($splitPartner, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval(min(0.5, $dealPercentage / 100)));
})->with([
    20,
    40,
    60,
    80,
    100,
]);