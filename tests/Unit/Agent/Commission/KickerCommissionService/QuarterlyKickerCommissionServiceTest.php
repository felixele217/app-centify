<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Plan;
use App\Services\Commission\KickerCommissionService;

it('incorporates the kicker if its target is met within the current quarter', function (float $quotaAttainment) {
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

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::QUARTERLY, $quotaAttainment))->toBe(intval(round($expectedKickerCommission)));
})->with([
    2, 3,
]);

it('does not grant the kicker if the target is not reached within the current quarter', function () {
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

    expect((new KickerCommissionService())->calculate($plan->agents()->first(), TimeScopeEnum::QUARTERLY, 1))->toBe(0);
});
