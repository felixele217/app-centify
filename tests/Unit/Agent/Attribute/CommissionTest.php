<?php

use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('calculates the commission properly', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals()->create());

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 12);
});

