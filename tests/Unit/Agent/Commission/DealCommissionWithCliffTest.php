<?php

use App\Enum\TimeScopeEnum;
use App\Models\Plan;
use App\Services\Commission\CommissionFromQuotaService;

it('returns normal commission even if cliff threshold is not met', function (TimeScopeEnum $timeScope, float $achievedQuotaThisTimeScope) {
    $plan = Plan::factory()->hasAgents()->hasCliff([
        'threshold_in_percent' => 20,
    ])->active()->create();

    expect((new CommissionFromQuotaService())->calculate($plan->agents()->first(), $timeScope, $achievedQuotaThisTimeScope))->not()->toBe(0);
})->with([
    [TimeScopeEnum::MONTHLY, 0.15],
    [TimeScopeEnum::QUARTERLY, 0.15],
    [TimeScopeEnum::ANNUALY, 0.15],
]);
