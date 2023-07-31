<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\CommissionFromQuotaService;

it('calculates the commission correctly for the respective scopes', function (TimeScopeEnum $timeScope) {
    $agent = Agent::factory()->create([
        'base_salary' => $baseSalary = 80_000_00,
        'on_target_earning' => $onTargetEarning = 200_000_00,
    ]);

    $expectedCommission = (($onTargetEarning - $baseSalary) / (12 / $timeScope->monthCount()));

    expect((new CommissionFromQuotaService())->calculate($agent, $timeScope, 1))->toBe($expectedCommission);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
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
