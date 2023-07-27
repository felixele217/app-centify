<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\DealCommissionService;

it('calculates the commission correctly for the respective scopes', function (TimeScopeEnum $timeScope, int $unitsInYear) {
    $testQuota = 1;

    $agent = Agent::factory()->create([
        'base_salary' => $baseSalary = 80_000_00,
        'on_target_earning' => $onTargetEarning = 200_000_00,
    ]);

    $expectedCommission = $testQuota * (($onTargetEarning - $baseSalary) / $unitsInYear);

    expect((new DealCommissionService())->calculate($agent, $timeScope, 1))->toBe($expectedCommission);
})->with([
    [TimeScopeEnum::MONTHLY, 12],
    [TimeScopeEnum::QUARTERLY, 4],
    [TimeScopeEnum::ANNUALY, 1],
]);
