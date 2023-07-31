<?php

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\CommissionChangeService;
use App\Services\Commission\CommissionFromQuotaService;
use Carbon\Carbon;

it('total commission change is calculated correctly for commission from quota', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    [$plan, $agent] = createActivePlanWithAgent($admin->organization->id, $quotaAttainmentThisMonth = 1.3);

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

it('returns null if there was no active plan last time scope', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'start_date' => Carbon::now()->firstOfMonth(),
        'end_date' => Carbon::now()->lastOfMonth(),
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(1, [
        'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
        'accepted_at' => DateHelper::dateInPreviousTimeScope($timeScope),
        'value' => $plan->target_amount_per_month / 2,
    ])->create([
        'organization_id' => $admin->organization->id,
    ]));

    Deal::factory()->create([
        'add_time' => Carbon::now(),
        'accepted_at' => Carbon::now(),
        'agent_id' => $agent->id,
        'value' => $plan->target_amount_per_month,
    ]);

    expect((new CommissionChangeService())->calculate($agent, $timeScope))->toBeNull();
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
