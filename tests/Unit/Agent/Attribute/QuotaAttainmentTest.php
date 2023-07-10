<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

beforeEach(function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);
});

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

it('calculates the quota attainment for the current month if scoped', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now(),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::parse('-1 month'),
    ]);

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::parse('+1 month'),
    ]);

    expect($agent->quota_attainment)->toBe($agent->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $plan->target_amount_per_month);
});
