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

it('calculates the quota attainment for the current quarter if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::QUARTERLY->value);

    $plan = Plan::factory()->create();

    Carbon::now()->nthOfQuarter(10, 5);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentQuarterDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->nthOfQuarter(1, Carbon::MONDAY),
            Carbon::now()->nthOfQuarter(6, Carbon::TUESDAY),
            Carbon::now()->nthOfQuarter(11, Carbon::SATURDAY),
        ]),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->subQuarter(),
    ]);

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->addQuarter(),
    ]);

    $currentQuarterDealsQuery = $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfQuarter(), Carbon::now()->endOfQuarter()]);

    expect($currentQuarterDealsQuery->count())->toBe($currentQuarterDealCount);

    expect($agent->quota_attainment)->toBe($currentQuarterDealsQuery->sum('value') / $plan->target_amount_per_month * 3);
});
