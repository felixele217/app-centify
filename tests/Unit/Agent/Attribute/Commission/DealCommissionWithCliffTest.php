<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\Commission\DealCommissionService;
use Carbon\Carbon;

it('returns deal commission of 0 if the cliff threshold is bigger than the achieved quota', function (int $dealValue, int $targetAmountPerMonth, float $cliffThreshold) {
    $agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now(),
        'value' => $dealValue,
    ])->create();

    $plan = Plan::factory()->hasCliff([
        'threshold_in_percent' => $cliffThreshold,
    ])->create([
        'start_date' => Carbon::parse('-2 days'),
        'target_amount_per_month' => $targetAmountPerMonth,
    ]);

    $plan->agents()->attach($agent);

    expect((new DealCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(0);
})->with([
    [
        1_000_00,
        10_000_00,
        0.25,
    ],
    [
        4_000,
        100_000_00,
        0.1,
    ],
]);

it('returns normal commission if cliff threshold equals quota attainment', function () {
    $agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now(),
        'value' => 500_00,
    ])->create();

    $plan = Plan::factory()->hasCliff([
        'threshold_in_percent' => 0.1,
    ])->create([
        'start_date' => Carbon::parse('-2 days'),
        'target_amount_per_month' => 10_000_00,
    ]);

    $plan->agents()->attach($agent);

    expect((new DealCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->not()->toBe(0);
});
