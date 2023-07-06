<?php

use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('calculates the quota attainment properly', function (int $targetAmountPerMonth, int $value, int $numberOfDeals) {
    $plan = Plan::factory()->create(['target_amount_per_month' => $targetAmountPerMonth]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($numberOfDeals, [
        'value' => $value,
        'add_time' => Carbon::today(),
    ])->create());

    expect($agent->quota_attainment)->toBe($value * $numberOfDeals / $targetAmountPerMonth);
})->with([
    [
        'target_amount_per_month' => 40_000_00,
        'value' => 20_000_00,
        'number_of_deals' => 2,
    ],
    [
        'target_amount_per_month' => 60_000_00,
        'value' => 20_000_00,
        'number_of_deals' => 2,
    ],
    [
        'target_amount_per_month' => 30_000_00,
        'value' => 10_000_00,
        'number_of_deals' => 5,
    ],
]);
