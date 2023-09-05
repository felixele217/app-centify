<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\TotalCommissionService;
use Carbon\Carbon;

it('returns the correct commission for an agent with multiple differently weighted plans with kicker and cap and paid leaves', function () {
    $timeScope = TimeScopeEnum::QUARTERLY;

    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $sdrPlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 100,
            'payout_in_percent' => 20,
        ])
        ->hasCap([
            'value' => 150_000_00,
        ])
        ->create([
            'target_amount_per_month' => $sdrPlanTargetAmountPerMonth = 150_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $aePlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 150,
            'payout_in_percent' => 20,
        ])
        ->hasCap([
            'value' => 150_000_00,
        ])
        ->create([
            'target_amount_per_month' => $aePlanTargetAmountPerMonth = 50_000_00,
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

    Deal::factory(3)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => $sdrPlanTargetAmountPerMonth * $timeScope->monthCount(),
            'add_time' => Carbon::now(),
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create([
            'value' => $aePlanTargetAmountPerMonth * $timeScope->monthCount(),
        ]);

    AgentPlan::factory()->create([
        'plan_id' => $aePlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 70,
    ]);

    AgentPlan::factory()->create([
        'plan_id' => $sdrPlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 30,
    ]);

    foreach ([AgentStatusEnum::SICK, AgentStatusEnum::VACATION] as $index => $reason) {
        $agent->paidLeaves()->create([
            'reason' => $reason->value,
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1 + $index * 5),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(4 + $index * 5),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    $paidLeaveCommissionPerDay = 10_000_00 / ContinuationOfPayTimeScopeEnum::QUARTER->amountOfDays();

    expect((new TotalCommissionService($timeScope))->calculate($agent))->toBe(
        intval(round(
            30_000_00 // SDR Plan Commission (5 Capped Deals) + AE Plan Commission weighted with share of variable pay
            + (50_000_00 / 4) * 0.2 // KickerCommission (quarterly)
            + 8 * $paidLeaveCommissionPerDay // Paid Leave Commission (8 days sick/vacation)
        ))
    );
});

it('returns the correct commission for an agent with multiple differently weighted plans with kicker and cap for previous timescope', function () {
    $timeScope = TimeScopeEnum::QUARTERLY;
    $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope($timeScope);

    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $sdrPlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 100,
            'payout_in_percent' => 20,
        ])
        ->hasCap([
            'value' => 150_000_00,
        ])
        ->create([
            'target_amount_per_month' => $sdrPlanTargetAmountPerMonth = 150_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $aePlan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 150,
            'payout_in_percent' => 20,
        ])
        ->hasCap([
            'value' => 150_000_00,
        ])
        ->create([
            'target_amount_per_month' => $aePlanTargetAmountPerMonth = 50_000_00,
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

    Deal::factory(3)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, $dateInPreviousTimeScope)
        ->create([
            'value' => $sdrPlanTargetAmountPerMonth * $timeScope->monthCount(),
            'add_time' => $dateInPreviousTimeScope,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, $dateInPreviousTimeScope)
        ->won($dateInPreviousTimeScope)
        ->create([
            'value' => $aePlanTargetAmountPerMonth * $timeScope->monthCount(),
        ]);

    AgentPlan::factory()->create([
        'plan_id' => $aePlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 70,
    ]);

    AgentPlan::factory()->create([
        'plan_id' => $sdrPlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 30,
    ]);

    foreach ([AgentStatusEnum::SICK, AgentStatusEnum::VACATION] as $index => $reason) {
        $agent->paidLeaves()->create([
            'reason' => $reason->value,
            'start_date' => $dateInPreviousTimeScope->firstOfMonth()->addWeekdays(1 + $index * 5),
            'end_date' => $dateInPreviousTimeScope->firstOfMonth()->addWeekdays(4 + $index * 5),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    $paidLeaveCommissionPerDay = 10_000_00 / ContinuationOfPayTimeScopeEnum::QUARTER->amountOfDays();

    expect((new TotalCommissionService($timeScope, $dateInPreviousTimeScope))->calculate($agent))->toBe(
        intval(round(
            30_000_00 // SDR Plan Commission (5 Capped Deals) + AE Plan Commission weighted with share of variable pay
            + (50_000_00 / 4) * 0.2 // KickerCommission (quarterly)
            + 8 * $paidLeaveCommissionPerDay // Paid Leave Commission (8 days sick/vacation)
        ))
    );
});
