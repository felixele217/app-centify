<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\TotalQuotaCommissionChangeService;
use App\Services\Commission\TotalQuotaCommissionService;
use Carbon\Carbon;

it('total commission change is calculated correctly for commission from quota', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    [$plan, $agent] = createActivePlanWithAgent($admin->organization->id, 1.3, TriggerEnum::DEMO_SCHEDULED);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, DateHelper::dateInPreviousTimeScope($timeScope))
        ->create([
            'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
        ]);

    $commissionThisTimeScope = (new TotalQuotaCommissionService($timeScope))->calculate($agent);
    $commissionPreviousTimeScope = (new TotalQuotaCommissionService($timeScope))->calculate($agent);

    expect((new TotalQuotaCommissionChangeService())->calculate($agent, $timeScope))->toBe(
        intval($commissionThisTimeScope - $commissionPreviousTimeScope)
    );
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

    $plan->agents()->attach($agent = Agent::factory()->create([
        'organization_id' => $admin->organization_id,
    ]));

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, DateHelper::dateInPreviousTimeScope($timeScope))
        ->create([
            'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
            'value' => $plan->target_amount_per_month / 2,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $plan->target_amount_per_month,
        ]);

    expect((new TotalQuotaCommissionChangeService())->calculate($agent, $timeScope))->toBeNull();
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
