<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\CommissionFromQuotaService;

it('calculates the commission correctly for the respective scopes', function (TimeScopeEnum $timeScope, float $quotaAttainment) {
    $agent = Agent::factory()->create([
        'base_salary' => $baseSalary = 80_000_00,
        'on_target_earning' => $onTargetEarning = 200_000_00,
    ]);

    $expectedCommissionForFullQuota = (($onTargetEarning - $baseSalary) / (12 / $timeScope->monthCount()));

    expect((new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainment))->toBe(intval($expectedCommissionForFullQuota * $quotaAttainment));
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 1],
    [TimeScopeEnum::ANNUALY, 1],

    [TimeScopeEnum::MONTHLY, 0.3],
    [TimeScopeEnum::QUARTERLY, 0.4],
    [TimeScopeEnum::ANNUALY, 1.3],
    [TimeScopeEnum::ANNUALY, 2.5],
    [TimeScopeEnum::ANNUALY, 0.7],
]);

it('calculates the commission correctly for the respective scopes if the quota attainment in the timescope was null', function (TimeScopeEnum $timeScope) {
    $agent = Agent::factory()->create([
        'base_salary' => 80_000_00,
        'on_target_earning' => 200_000_00,
    ]);

    expect((new CommissionFromQuotaService())->calculate($agent, $timeScope, null))->toBeNull();
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
