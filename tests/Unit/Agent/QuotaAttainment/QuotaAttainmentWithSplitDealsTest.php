<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly with for splitted deals', function (int $sharedPercentage1, ?int $sharedPercentage2) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(1)->state([
            'accepted_at' => Carbon::now()->firstOfMonth(),
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ]))->create());

    $agent->deals()->first()->splits()->create([
        'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
        'shared_percentage' => $sharedPercentage1,
    ]);

    if ($sharedPercentage2) {
        $agent->deals()->first()->splits()->create([
            'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
            'shared_percentage' => $sharedPercentage2,
        ]);
    }

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval(1 - (($sharedPercentage1 + $sharedPercentage2) / 100)));
})->with([
    [20, 30],
    [50, 40],
    [10, 5],
    [50, null],
    [50, 50],
]);

it('minimum quota of the agent is 0', function () {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(1)->state([
            'accepted_at' => Carbon::now()->firstOfMonth(),
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ]))->create());

    $agent->deals()->first()->splits()->create([
        'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
        'shared_percentage' => 200,
    ]);

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval(0));
});

it('split partner also get the quota', function (int $sharedPercentage) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(1)->state([
            'accepted_at' => Carbon::now()->firstOfMonth(),
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ]))->create());

    $agent->deals()->first()->splits()->create([
        'agent_id' => $splitPartner = Agent::factory()
            ->has(
                Plan::factory()->active()->count(1)
            )
            ->ofOrganization($agent->organization_id)
            ->create(),
        'shared_percentage' => $sharedPercentage,
    ]);

    expect((new QuotaAttainmentService())->calculate($splitPartner, TimeScopeEnum::MONTHLY))->toBe(floatval($sharedPercentage / 100));
})->with([
    20, 40, 60, 80, 100,
])->todo();
