<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current month', function () {
    $agent = Agent::factory()->create();

    foreach ($iterations = [0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => $paidLeaveStartDate = Carbon::parse('-10 days'),
            'end_date' => $paidLeaveEndDate = Carbon::parse('-2 days'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDays = $paidLeaveEndDate->diffInWeekdays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDays * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(intval(round($paidLeaveCommission * count($iterations))));
});
