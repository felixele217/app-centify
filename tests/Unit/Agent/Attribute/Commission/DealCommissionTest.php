<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

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

    expect($agent->commission)->toBe(intval(round($agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary) / 12)));
});

it('correctly calculates the commission for the current month if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentMonthDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth(),
            Carbon::now()->lastOfMonth(),
        ]),
        'value' => 1_000_00,
    ])->create([
        'base_salary' => 80_000_00,
        'on_target_earning' => 200_000_00,
    ]));

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth()->subDay(1),
            Carbon::now()->lastOfMonth()->addDay(1),
        ]),
    ]);

    expect($agent->commission)->toBe(20_000_00);
});

it('correctly calculates the commission for the current quarter if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::QUARTERLY->value);

    $plan = Plan::factory()->create([
        'target_amount_per_month' => 2_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->nthOfQuarter(1, Carbon::MONDAY),
            Carbon::now()->nthOfQuarter(6, Carbon::TUESDAY),
            Carbon::now()->nthOfQuarter(11, Carbon::SATURDAY),
        ]),
        'value' => 1_500_00,
    ])->create([
        'base_salary' => 80_000_00,
        'on_target_earning' => 200_000_00,
    ]));

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->subQuarter(),
            Carbon::now()->addQuarter(),
        ]),
    ]);

    expect(intval($agent->commission))->toBe(15_000_00);
});

it('correctly calculates the commission for the current year if scoped', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::ANNUALY->value);

    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->lastOfYear(),
            Carbon::now()->firstOfYear(),
            Carbon::now(),
        ]),
        'value' => 3_000_00,
    ])->create([
        'base_salary' => 80_000_00,
        'on_target_earning' => 200_000_00,
    ]));

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->lastOfYear()->addDay(1),
            Carbon::now()->firstOfYear()->subDay(1),
        ]),
    ]);

    expect(intval($agent->commission))->toBe(60_000_00);
});
