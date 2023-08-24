<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;
use App\Services\Commission\TotalQuotaCommissionService;
use Carbon\Carbon;

it('returns the combined quota commissions of all plans of the user', function (TimeScopeEnum $timeScope) {
    $agent = Agent::factory()->create();

    $sdrPlan = Plan::factory()->active()
        ->create([
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]);
    $aePlan = Plan::factory()->active()
        ->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

    $demoScheduledDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
        ]);
    $dealWonDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create();

    $sdrPlan->agents()->attach($agent);
    $aePlan->agents()->attach($agent);

    expect((new TotalQuotaCommissionService())->calculate($agent, $timeScope))->toBe(
        (new PlanQuotaCommissionService())->calculate($agent, $sdrPlan, $timeScope)
        + (new PlanQuotaCommissionService())->calculate($agent, $aePlan, $timeScope)
    );
})->with(TimeScopeEnum::cases());

it('returns 0 for the quota commission if the user has no plans', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->id,
    ]);

    expect((new TotalQuotaCommissionService())->calculate($agent, $timeScope, 0))->toBe(0);
})->with(TimeScopeEnum::cases());
