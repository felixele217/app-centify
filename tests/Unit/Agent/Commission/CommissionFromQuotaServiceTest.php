<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\CommissionFromQuotaService;

it('calculates the commission correctly for the respective scopes', function (TimeScopeEnum $timeScope) {
    $testQuota = 1;

    $agent = Agent::factory()->create([
        'base_salary' => $baseSalary = 80_000_00,
        'on_target_earning' => $onTargetEarning = 200_000_00,
    ]);

    $expectedCommission = $testQuota * (($onTargetEarning - $baseSalary) / (12 / $timeScope->monthCount()));

    expect((new CommissionFromQuotaService())->calculate($agent, $timeScope, 1))->toBe($expectedCommission);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
