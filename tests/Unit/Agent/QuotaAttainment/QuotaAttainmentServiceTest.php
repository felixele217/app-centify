<?php

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Agent;
use App\Enum\TimeScopeEnum;
use App\Services\QuotaAttainmentService;

it('calculates the quota attainment properly for the active plan with the most recent start_date', function () {
    $agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::now(),
    ])->create();

    $oldPlan = Plan::factory()->create([
        'start_date' => Carbon::parse('-2 days'),
    ]);

    $latestPlan = Plan::factory()->create([
        'start_date' => Carbon::yesterday(),
    ]);

    $agent->plans()->attach(Plan::all());

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval($agent->deals->sum('value') / $latestPlan->target_amount_per_month));
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

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval($agent->deals()->whereNotNull('accepted_at')->sum('value') / $plan->target_amount_per_month));
});

it('calculates the quota attainment for the current month if scoped', function () {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentMonthDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth(),
            Carbon::now()->lastOfMonth(),
        ]),
        'value' => 1_000_00,
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => fake()->randomElement([
            Carbon::now()->firstOfMonth()->subDay(1),
            Carbon::now()->lastOfMonth()->addDay(1),
        ]),
    ]);

    $currentMonthDealsQuery = $agent->deals()->whereMonth('accepted_at', Carbon::now()->month);

    expect($currentMonthDealsQuery->count())->toBe($currentMonthDealCount);
    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval(2));
});

it('calculates the quota attainment for the current quarter if scoped', function () {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentQuarterDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->nthOfQuarter(1, Carbon::MONDAY),
            Carbon::now()->nthOfQuarter(6, Carbon::TUESDAY),
            Carbon::now()->nthOfQuarter(11, Carbon::SATURDAY),
        ]),
        'value' => 1_500_00,
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

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::QUARTERLY))->toBe(floatval(1));
});

it('calculates the quota attainment for the current year if scoped', function () {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($currentYearDealCount = 2, [
        'accepted_at' => fake()->randomElement([
            Carbon::now()->lastOfYear(),
            Carbon::now()->firstOfYear(),
            Carbon::now(),
        ]),
        'value' => 3_000_00,
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->lastOfYear()->addDay(1),
    ]);

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => Carbon::now()->firstOfYear()->subDay(1),
    ]);

    $currentYearDealsQuery = $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()]);

    expect($currentYearDealsQuery->count())->toBe($currentYearDealCount);

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(0.5);
});

it('does not throw any errors when agents have no base_salary or no quota_attainment or no plan', function ($baseSalary, $onTargetEarning) {
    $agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::now(),
    ])->create([
        'on_target_earning' => $onTargetEarning,
        'base_salary' => $baseSalary,
    ]);

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->not()->toBeNull();
})->with([
    [10_000_00, 10_000_00],
    [0, 10_000_00],
    [10_000_00, 0],
]);

it('returns 0 if the agent has no active plan', function () {
    signInAdmin();

    expect((new QuotaAttainmentService())->calculate(Agent::factory()->create(), TimeScopeEnum::MONTHLY, 0))->toBe(floatval(0));
});
