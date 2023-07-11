<?php

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Agent;
use App\Enum\TimeScopeEnum;

it('calculates the commission properly for the active plan with the most recent start_date', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::now(),
    ])->create();

    Plan::factory()->create([
        'start_date' => Carbon::parse('-2 days'),
    ]);

    Plan::factory()->create([
        'start_date' => Carbon::yesterday(),
    ]);

    $agent->plans()->attach(Plan::all());

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 12);
});

it('correctly calculates the commission for the current month if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentMonthDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth(),
            Carbon::now()->lastOfMonth(),
        ]),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth()->subDay(1),
            Carbon::now()->lastOfMonth()->addDay(1),
        ]),
    ]);

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 12);
});

it('correctly calculates the commission for the current quarter if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::QUARTERLY->value);

    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->nthOfQuarter(1, Carbon::MONDAY),
            Carbon::now()->nthOfQuarter(6, Carbon::TUESDAY),
            Carbon::now()->nthOfQuarter(11, Carbon::SATURDAY),
        ]),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->subQuarter(),
            Carbon::now()->addQuarter(),
        ]),
    ]);

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 4);
});

it('correctly calculates the commission for the current year if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::ANNUALY->value);

    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentYearDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->lastOfYear(),
            Carbon::now()->firstOfYear(),
            Carbon::now(),
        ]),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->lastOfYear()->addDay(1),
            Carbon::now()->firstOfYear()->subDay(1),
        ]),
    ]);

    expect($agent->commission)->toBe($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary));
});
