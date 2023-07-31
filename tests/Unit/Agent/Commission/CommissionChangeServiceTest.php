<?php

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Deal;
use App\Services\Commission\CommissionChangeService;
use App\Services\Commission\CommissionFromQuotaService;

it('total commission change is calculated correctly for commission from quota', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    [$plan, $agent] = createPlanWithAgent($admin->organization->id, $quotaAttainmentThisMonth = 1.3);

    Deal::factory()->create([
        'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
        'accepted_at' => DateHelper::dateInPreviousTimeScope($timeScope),
        'value' => $plan->target_amount_per_month * $quotaAttainmentMonthInPreviousTimeScope = 0.7,
        'agent_id' => $agent->id,
    ]);

    $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentThisMonth / $timeScope->monthCount());
    $commissionPreviousTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentMonthInPreviousTimeScope / $timeScope->monthCount());

    expect((new CommissionChangeService())->calculate($agent, $timeScope))->toBe(intval($commissionThisTimeScope - $commissionPreviousTimeScope));
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
