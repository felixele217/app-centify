<?php

use App\Enum\TimeScopeEnum;
use App\Models\Deal;
use App\Services\Commission\CommissionFromQuotaService;
use Carbon\Carbon;

it('total commission change is calculated correctly for simple deals', function () {
    $admin = signInAdmin();

    [$plan, $agent] = createPlanWithAgent($admin->organization->id, $quotaAttainmentThisMonth = 1.3);

    $agent->deals()->create(Deal::factory()->create([
        'add_time' => Carbon::now()->firstOfMonth()->subDays(5),
        'accepted_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'value' => $plan->target_amount_per_month * $quotaAttainmentLastMonth = 0.7,
    ]));

    $commissionThisMonth = (new CommissionFromQuotaService())->calculate($agent, TimeScopeEnum::MONTHLY, $quotaAttainmentThisMonth);
    $commissionLastMonth = (new CommissionFromQuotaService())->calculate($agent, TimeScopeEnum::MONTHLY, $quotaAttainmentLastMonth);

    expect((new CommissionChangeService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(intval($commissionThisMonth - $commissionLastMonth));
})->todo();
