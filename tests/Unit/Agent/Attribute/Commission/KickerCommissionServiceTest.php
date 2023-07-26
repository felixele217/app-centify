<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\KickerCommissionService;
use Carbon\Carbon;

it('incorporates the kicker if its quarterly target is met within the quarter', function (float $quotaAttainment) {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => $payoutInPercent = 25,
        ])
        ->hasAgents(1, [
            'base_salary' => $baseSalary = 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    $expectedKickerCommission = ($baseSalary / 12) * ($payoutInPercent / 100);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::QUARTERLY, $quotaAttainment))->toBe(intval(round($expectedKickerCommission)));
})->with([
    2, 3,
]);

it('incorporates the kicker if its quarterly target is met within a single month', function (float $quotaAttainment) {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => $payoutInPercent = 25,
        ])
        ->hasAgents(1, [
            'base_salary' => $baseSalary = 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    $expectedKickerCommission = ($baseSalary / 12) * ($payoutInPercent / 100);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::MONTHLY, $quotaAttainment))->toBe(intval(round($expectedKickerCommission)));
})->with([
    6, 7,
]);

it('does not grant the kicker if the quarterly target is not reached within the month for the monthly scope', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
        ])
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::MONTHLY, 5))->toBe(0);
});

it('incorporates the sum of the kicker values for all the kickers met within the year', function () {
})->todo();

it('does not incorporate the kicker if its quarterly target is not met because deals are outside of the time scope', function (TimeScopeEnum $timeScope, Carbon $dealAcceptedDate) {
    $admin = signInAdmin();

    $this->get(route('dashboard').'?time_scope='.$timeScope->value);

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
        ])
        ->hasAgents(1)
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    Deal::factory()->create([
        'agent_id' => $plan->agents()->first()->id,
        'value' => 60_000_000,
        'accepted_at' => $dealAcceptedDate,
    ]);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), $timeScope, $plan->agents()->first()->quota_attainment))->toBe(0);
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth()->subDays(1)],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter()->subDays(1)],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear()->subDays(1)],

    [TimeScopeEnum::MONTHLY, Carbon::now()->lastOfMonth()->addDays(1)],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->lastOfQuarter()->addDays(1)],
    [TimeScopeEnum::ANNUALY, Carbon::now()->lastOfYear()->addDays(1)],
]);

it('returns 0 for the kicker commission if the kicker is not achieved', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
        ])
        ->hasAgents(1)
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::QUARTERLY, 0))->toBe(0);
});

it('returns 0 for the kicker commission if the plan has no kicker', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::QUARTERLY, 10))->toBe(0);
});
