<?php

use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

it('returns the attained quota change in %', function () {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(2)->sequence([
            'accepted_at' => Carbon::now()->firstOfMonth()->subDays(1),
            'add_time' => Carbon::now()->firstOfMonth()->subDays(1),
            'value' => $targetAmountPerMonth / 2,
        ], [
            'accepted_at' => Carbon::now(),
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth,
        ]
        ))->create());

    expect($agent->quota_attainment_change)->toBe(0.5);
});
