<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current quarter for paid leaves within the current quarter', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->firstOfQuarter()->addDays(6),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfQuarter()->addDays(11),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $daysForAPaidLeaveThisQuarter = $paidLeaveEndDate->diffInWeekdays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / $continuationOfPayTimeScope->amountOfDays();

    $paidLeaveCommission = $daysForAPaidLeaveThisQuarter * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::QUARTERLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current quarter for the paid leave overlapping with last quarter', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => Carbon::now()->firstOfQuarter()->subDays(6),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfQuarter()->addDays(5),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $daysForAPaidLeaveThisQuarter = $paidLeaveEndDate->diffInWeekdays(Carbon::now()->firstOfMonth()) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / $continuationOfPayTimeScope->amountOfDays();

    $paidLeaveCommission = $daysForAPaidLeaveThisQuarter * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::QUARTERLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});
