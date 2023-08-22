<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('calculates the quota attainment correctly for the active plan with the most recent start_date', function () {
    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::yesterday())
        ->create([
            'add_time' => Carbon::yesterday(),
        ]);
    $agent = Agent::find($agentId);

    Plan::factory()->create([
        'start_date' => Carbon::parse('-2 days'),
    ]);

    $latestPlan = Plan::factory()->create([
        'start_date' => Carbon::yesterday(),
    ]);

    $agent->plans()->attach(Plan::all());

    expect((new QuotaAttainmentService($agent, TimeScopeEnum::MONTHLY))->calculate())->toBe(floatval($agent->deals->sum('value') / $latestPlan->target_amount_per_month));
});

it('calculates the quota attainment for the current scope', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $lastDateInScope)
        ->create([
            'add_time' => $lastDateInScope,
            'value' => 1_000_00,
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SET_BY, $firstDateInScope)
        ->create([
            'add_time' => $firstDateInScope,
            'value' => 1_000_00,
        ]);

    $agent = Agent::find($agentId);

    $plan->agents()->attach($agent);

    expect((new QuotaAttainmentService($agent, $timeScope))->calculate())->toBe(floatval(2 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()],
]);

it('does not use deals that lie out of the current time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    $plan = Plan::factory()->create();

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $firstDateInScope->subDays(5))
        ->create([
            'add_time' => $firstDateInScope->subDays(5),
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SET_BY, $lastDateInScope->addDays(5))
        ->create([
            'add_time' => $lastDateInScope->addDays(5),
        ]);
    $agent = Agent::find($agentId);
    $plan->agents()->attach($agent);

    expect((new QuotaAttainmentService($agent, $timeScope))->calculate())->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('returns null if the agent has no active plan', function () {
    signInAdmin();

    expect((new QuotaAttainmentService(Agent::factory()->create(), TimeScopeEnum::MONTHLY))->calculate())->toBeNull();
});

it('does not throw any errors when agents have no base_salary or no quota_attainment or no plan', function ($baseSalary, $onTargetEarning) {
    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create([
            'on_target_earning' => $onTargetEarning,
            'base_salary' => $baseSalary,
        ])->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now()->firstOfMonth(),
        ]);

    expect((new QuotaAttainmentService(Agent::find($agentId), TimeScopeEnum::MONTHLY))->calculate())->toBeNull();
})->with([
    [10_000_00, 10_000_00],
    [0, 10_000_00],
    [10_000_00, 0],
]);
