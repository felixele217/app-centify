<?php

use App\Models\Agent;
use App\Models\Plan;

it('calculates the quota attainment properly', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals()->create());

    expect($agent->quota_attainment)->toBe($agent->deals->sum('value') / $plan->target_amount_per_month);
});
