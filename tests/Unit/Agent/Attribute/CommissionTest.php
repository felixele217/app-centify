<?php

use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\PaidLeave;
use App\Models\Plan;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('sums the commissions of deals and paid leaves and kicker', function () {
    $admin = signInAdmin();

    [$plan, $agent] = createPlanWithAgent($admin->organization->id, $quotaAttainment = 6);

    $plan->kicker()->create([
        'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
        'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
        'threshold_in_percent' => 200,
        'payout_in_percent' => 25,
    ]);

    PaidLeave::factory()->create([
        'agent_id' => $agent->id,
        'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
        'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(4),
        'sum_of_commissions' => 10_000_00,
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
    ]);

    $variableSalaryPerMonth = ($agent->on_target_earning - $agent->base_salary) / 12;

    $expectedDealCommission = $quotaAttainment * $variableSalaryPerMonth;
    $expectedKickerCommission = ($agent->base_salary / 4) * 0.25;
    $expectedPaidLeaveCommission = (new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY);

    $expectedTotalCommission = $expectedDealCommission + $expectedKickerCommission + $expectedPaidLeaveCommission;

    expect($agent->commission)->toBe(intval($expectedTotalCommission));
});

// // Quota Attainment
// $plan = Plan::factory()->create([
//     'target_amount_per_month' => 1_000_00,
// ]);

// $plan->agents()->attach($agent = Agent::factory()->has(
//     Deal::factory()->count(2)->sequence([
//         'accepted_at' => $lastDateInScope,
//         'add_time' => $lastDateInScope,
//     ], [
//         'accepted_at' => $firstDateInScope,
//         'add_time' => $firstDateInScope,
//     ])->state([
//         'value' => 1_000_00,
//     ]))->create());

// expect((new QuotaAttainmentService())->calculate($agent, $timeScope))->toBe(floatval(2 / $timeScope->monthCount()));

// //DEAL COMMISSION
// $testQuota = 1;

// $agent = Agent::factory()->create([
//     'base_salary' => $baseSalary = 80_000_00,
//     'on_target_earning' => $onTargetEarning = 200_000_00,
// ]);

// $expectedTotalCommission = $testQuota * (($onTargetEarning - $baseSalary) / (12 / $timeScope->monthCount()));

// expect((new DealCommissionService())->calculate($agent, $timeScope, 1))->toBe($expectedTotalCommission);
