<?php

use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('returns the last created plan that is assigned to the agent', function () {
    $agent = Agent::factory()->create();

    Plan::factory()->create([
        'created_at' => Carbon::yesterday(),
        'name' => 'old plan',
    ]);

    Plan::factory()->create([
        'created_at' => Carbon::today(),
        'name' => $currentPlanName = 'current plan',
    ]);

    $agent->plans()->attach(Plan::all());

    expect($agent->currentPlan()->name)->toBe($currentPlanName);
});
