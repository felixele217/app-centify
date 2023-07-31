<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment properly for the active plan with the most recent start_date', function () {
    $agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::yesterday(),
        'add_time' => Carbon::yesterday(),
    ])->create();

    Plan::factory()->create([
        'start_date' => Carbon::parse('-2 days'),
    ]);

    $latestPlan = Plan::factory()->create([
        'start_date' => Carbon::yesterday(),
    ]);

    $agent->plans()->attach(Plan::all());

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval($agent->deals->sum('value') / $latestPlan->target_amount_per_month));
});

it('calculates the quota attainment for the current scope', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(2)->sequence([
            'accepted_at' => $lastDateInScope,
            'add_time' => $lastDateInScope,
        ], [
            'accepted_at' => $firstDateInScope,
            'add_time' => $firstDateInScope,
        ])->state([
            'value' => 1_000_00,
        ]))->create());

    expect((new QuotaAttainmentService())->calculate($agent, $timeScope))->toBe(floatval(2 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()],
]);

it('does not use deals that lie out of the current time scope', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(2)->sequence([
            'accepted_at' => $firstDateInScope->subDays(5),
            'add_time' => $firstDateInScope->subDays(5),
        ], [
            'accepted_at' => $lastDateInScope->addDays(5),
            'add_time' => $lastDateInScope->addDays(5),
        ])->state([
            'value' => 1_000_00,
        ]))->create());

    expect((new QuotaAttainmentService())->calculate($agent, $timeScope))->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()],
]);

it('returns null if the agent has no active plan', function () {
    signInAdmin();
   
    expect((new QuotaAttainmentService())->calculate(Agent::factory()->create(), TimeScopeEnum::MONTHLY, 0))->toBeNull();
});

it('does not throw any errors when agents have no base_salary or no quota_attainment or no plan', function ($baseSalary, $onTargetEarning) {
    $agent = Agent::factory()->hasDeals([
        'accepted_at' => Carbon::now(),
    ])->create([
        'on_target_earning' => $onTargetEarning,
        'base_salary' => $baseSalary,
    ]);

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBeNull();
})->with([
    [10_000_00, 10_000_00],
    [0, 10_000_00],
    [10_000_00, 0],
]);
