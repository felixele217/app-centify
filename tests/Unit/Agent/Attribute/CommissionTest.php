<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;

it('calculates the commission properly', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals()->create());

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 12);
});
