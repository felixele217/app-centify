<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Plan;
use App\Services\Commission\PlanKickerCommissionService;

it('incorporates the kicker if its target is met within the current month', function (float $quotaAttainment) {
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

    $expectedKickerCommission = ($baseSalary / 4) * ($payoutInPercent / 100);

    expect((new PlanKickerCommissionService(TimeScopeEnum::MONTHLY))->calculate($plan->agents()->first(), $plan, $quotaAttainment))->toBe(intval(round($expectedKickerCommission)));
})->with([
    6, 7,
]);

it('does not grant the kicker if the target is not reached within the current month', function () {
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

    expect((new PlanKickerCommissionService(TimeScopeEnum::MONTHLY))->calculate($plan->agents()->first(), $plan, 5))->toBe(0);
});
