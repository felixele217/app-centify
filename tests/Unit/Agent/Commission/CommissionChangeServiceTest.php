<?php

use App\Enum\TimeScopeEnum;
use App\Models\Deal;
use App\Services\Commission\CommissionChangeService;
use App\Services\Commission\CommissionFromQuotaService;
use Carbon\Carbon;

it('total commission change is calculated correctly for commission from quota', function (TimeScopeEnum $timeScope, Carbon $dateInPreviousTimeScope) {
    $admin = signInAdmin();

    [$plan, $agent] = createPlanWithAgent($admin->organization->id, $quotaAttainmentThisMonth = 1.3);

    Deal::factory()->create([
        'add_time' => $dateInPreviousTimeScope,
        'accepted_at' => $dateInPreviousTimeScope,
        'value' => $plan->target_amount_per_month * $quotaAttainmentMonthInPreviousTimeScope = 0.7,
        'agent_id' => $agent->id,
    ]);

    $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentThisMonth / $timeScope->monthCount());
    $commissionPreviousTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentMonthInPreviousTimeScope / $timeScope->monthCount());

    expect((new CommissionChangeService())->calculate($agent, $timeScope))->toBe(intval($commissionThisTimeScope - $commissionPreviousTimeScope));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth()->subDays(5)],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter()->subDays(5)],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear()->subDays(5)],
]);
