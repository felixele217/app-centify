<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->firstOfMonth()->addDays(5),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfMonth()->addDays(10),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDays = $paidLeaveEndDate->diffInWeekdays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDays * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current month for the paid leave overlapping with last month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => Carbon::now()->firstOfMonth()->subDays(5),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfMonth()->addDays(6),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDaysThisMonth = $paidLeaveEndDate->diffInWeekdays(Carbon::now()->firstOfMonth()) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDaysThisMonth * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current month for the paid leave overlapping with next month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->lastOfMonth()->subDays(5),
            'end_date' => Carbon::now()->lastOfMonth()->addDays(6),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDaysThisMonth = Carbon::now()->lastOfMonth()->diffInWeekdays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDaysThisMonth * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});
