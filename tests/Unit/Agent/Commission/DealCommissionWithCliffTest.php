<?php

use App\Enum\TimeScopeEnum;
use App\Models\Plan;
use App\Services\Commission\CommissionFromQuotaService;

it('returns deal commission of 0 if the cliff threshold is bigger than the achieved quota', function (float $achievedQuota, int $cliffThreshold) {
    $plan = Plan::factory()->hasAgents()->hasCliff([
        'threshold_in_percent' => $cliffThreshold,
    ])->active()->create();

    expect((new CommissionFromQuotaService())->calculate($plan->agents()->first(), TimeScopeEnum::MONTHLY, $achievedQuota))->toBe(0);
})->with([
    [0.24, 25],
    [0.05, 10],
]);

it('returns normal commission if cliff threshold equals quota attainment', function () {
    $plan = Plan::factory()->hasAgents()->hasCliff([
        'threshold_in_percent' => 20,
    ])->active()->create();

    expect((new CommissionFromQuotaService())->calculate($plan->agents()->first(), TimeScopeEnum::MONTHLY, 0.5))->not()->toBeNull();
});
