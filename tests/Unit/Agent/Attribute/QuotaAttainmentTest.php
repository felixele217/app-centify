<?php

use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

it('calculates the quota attainment properly', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::now(),
    ])->create());

    expect($agent->quota_attainment)->toBe($agent->deals->sum('value') / $plan->target_amount_per_month);
});

it('calculates the quota attainment only for accepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now(),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => null,
    ]);

    expect($agent->quota_attainment)->toBe($agent->deals()->whereNotNull('accepted_at')->sum('value') / $plan->target_amount_per_month);
});
