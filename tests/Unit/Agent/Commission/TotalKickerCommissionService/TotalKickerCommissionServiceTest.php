<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanKickerCommissionService;
use App\Services\Commission\TotalKickerCommissionService;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;
use Carbon\Carbon;

it('returns the combined kicker commissions of all plans of the user', function (TimeScopeEnum $timeScope, int $factor) {
    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $sdrPlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
        ])
        ->create([
            'target_amount_per_month' => $planTarget = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $demoScheduledDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $planTarget * 2 * $timeScope->monthCount() * $factor,
        ]);

    $aePlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
        ])
        ->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

    $dealWonDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create([
            'value' => $planTarget * 2 * $timeScope->monthCount() * $factor,
        ]);

    $sdrPlan->agents()->attach($agent);
    $aePlan->agents()->attach($agent);

    expect((new TotalKickerCommissionService($timeScope))->calculate($agent))->toBeGreaterThan(0);

    expect((new TotalKickerCommissionService($timeScope))->calculate($agent))->toBe(
        (new PlanKickerCommissionService($timeScope))->calculate($agent, $sdrPlan, (new PlanQuotaAttainmentService($agent, $sdrPlan, $timeScope))->calculate())
        + (new PlanKickerCommissionService($timeScope))->calculate($agent, $aePlan, (new PlanQuotaAttainmentService($agent, $aePlan, $timeScope))->calculate())
    );
})->with([
    [TimeScopeEnum::MONTHLY, 3],
    [TimeScopeEnum::QUARTERLY, 1],
]);

it('returns test the combined kicker commissions of all plans of the user for a past timescope', function (TimeScopeEnum $timeScope, int $factor) {
    $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope($timeScope);

    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $sdrPlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
        ])
        ->create([
            'target_amount_per_month' => $planTarget = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
            'start_date' => $dateInPreviousTimeScope,
            'end_date' => $dateInPreviousTimeScope->endOfMonth(),
        ]);

    $demoScheduledDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, $dateInPreviousTimeScope)
        ->create([
            'add_time' => $dateInPreviousTimeScope,
            'value' => $planTarget * 2 * $timeScope->monthCount() * $factor,
        ]);

    $aePlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
        ])
        ->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
            'start_date' => $dateInPreviousTimeScope,
            'end_date' => $dateInPreviousTimeScope->endOfMonth(),
        ]);

    $dealWonDealToAchieveKickerCommission = Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, $dateInPreviousTimeScope)
        ->won($dateInPreviousTimeScope)
        ->create([
            'value' => $planTarget * 2 * $timeScope->monthCount() * $factor,
        ]);

    $sdrPlan->agents()->attach($agent);
    $aePlan->agents()->attach($agent);

    expect((new TotalKickerCommissionService($timeScope, $dateInPreviousTimeScope))->calculate($agent))->toBeGreaterThan(0);

    expect((new TotalKickerCommissionService($timeScope, $dateInPreviousTimeScope))->calculate($agent))->toBe(
        (new PlanKickerCommissionService($timeScope))->calculate($agent, $sdrPlan, (new PlanQuotaAttainmentService($agent, $sdrPlan, $timeScope, $dateInPreviousTimeScope))->calculate())
        + (new PlanKickerCommissionService($timeScope))->calculate($agent, $aePlan, (new PlanQuotaAttainmentService($agent, $aePlan, $timeScope, $dateInPreviousTimeScope))->calculate())
    );
})->with([
    [TimeScopeEnum::MONTHLY, 3],
    [TimeScopeEnum::QUARTERLY, 1],
]);

it('returns null for the kicker commission if user has no plans', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->id,
    ]);

    expect((new TotalKickerCommissionService($timeScope))->calculate($agent))->toBe(0);
})->with(TimeScopeEnum::cases());
