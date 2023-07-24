<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current year for paid leaves within the current year', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->firstOfYear()->addDays(6),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfYear()->addDays(11),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $daysForAPaidLeaveThisYear = $paidLeaveEndDate->diffInWeekdays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $daysForAPaidLeaveThisYear * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current year for the paid leave overlapping with last year', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => Carbon::now()->firstOfYear()->subDays(6),
            'end_date' => $paidLeaveEndDate = Carbon::now()->firstOfYear()->addDays(5),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $daysForAPaidLeaveThisYear = $paidLeaveEndDate->diffInWeekdays(Carbon::now()->firstOfYear()) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $daysForAPaidLeaveThisYear * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});

it('calculates the commission with the additional paid leave value for the current year for the paid leave overlapping with next year', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $paidLeaveStartDate = Carbon::now()->lastOfYear()->subDays(6),
            'end_date' => Carbon::now()->lastOfYear()->addDays(5),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $daysForAPaidLeaveThisYear = $paidLeaveStartDate->diffInWeekdays(Carbon::now()->lastOfYear()) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $daysForAPaidLeaveThisYear * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});
