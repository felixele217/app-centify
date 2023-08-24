<?php

use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\PaidLeave;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('sums the commissions of deals and paid leaves and kicker correctly for the current month and quarter', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    $this->get(route('dashboard').'?time_scope='.$timeScope->value);

    [$plan, $agent] = createActivePlanWithAgent($admin->organization->id, $quotaAttainmentPerMonth = 6, TriggerEnum::DEMO_SCHEDULED);

    $plan->kicker()->create([
        'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
        'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
        'threshold_in_percent' => 200,
        'payout_in_percent' => 25,
        'time_scope' => TimeScopeEnum::QUARTERLY->value,
    ]);

    PaidLeave::factory()->create([
        'agent_id' => $agent->id,
        'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
        'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(4),
        'sum_of_commissions' => 10_000_00,
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
    ]);

    $variableSalaryPerMonth = ($agent->on_target_earning - $agent->base_salary) / 12;

    $expectedCommissionFromQuota = $quotaAttainmentPerMonth * $variableSalaryPerMonth;
    $expectedKickerCommission = ($agent->base_salary / 4) * 0.25;
    $expectedPaidLeaveCommission = (new PaidLeaveCommissionService())->calculate($agent, $timeScope);

    $expectedTotalCommission = $expectedCommissionFromQuota + $expectedKickerCommission + $expectedPaidLeaveCommission;

    expect($agent->commission)->toBe(intval($expectedTotalCommission));
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
]);

it('sums the commissions of deals and paid leaves and kicker correctly for the current year')->todo();
