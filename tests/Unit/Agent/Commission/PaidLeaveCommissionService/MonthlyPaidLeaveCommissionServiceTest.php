<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->firstOfMonth()->addDays(5),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfMonth()->addDays(10),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDays = DateHelper::weekdayCount($paidLeaveStartDate, $paidLeaveEndDate);
    $expectedCommissionsPerDay = $sumOfCommissions / $continuationOfPayTimeScope->amountOfDays();

    $paidLeaveCommission = $paidLeaveDays * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService(TimeScopeEnum::MONTHLY))->calculate($agent))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current month for the paid leave overlapping with last month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => Carbon::now()->firstOfMonth()->subDays(5),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfMonth()->addDays(6),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDaysThisMonth = DateHelper::weekdayCount(Carbon::now()->firstOfMonth(), $paidLeaveEndDate);
    $expectedCommissionsPerDay = $sumOfCommissions / $continuationOfPayTimeScope->amountOfDays();

    $paidLeaveCommission = $paidLeaveDaysThisMonth * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService(TimeScopeEnum::MONTHLY))->calculate($agent))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current month for the paid leave overlapping with next month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->lastOfMonth()->subDays(5),
            'end_date' => Carbon::now()->lastOfMonth()->addDays(6),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDaysThisMonth = DateHelper::weekdayCount($paidLeaveStartDate, Carbon::now()->lastOfMonth());
    $expectedCommissionsPerDay = $sumOfCommissions / $continuationOfPayTimeScope->amountOfDays();

    $paidLeaveCommission = $paidLeaveDaysThisMonth * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService(TimeScopeEnum::MONTHLY))->calculate($agent))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});
