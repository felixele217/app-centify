<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the paid leave commission just for the employee with the paid leaves', function (TimeScopeEnum $timeScope, Carbon $startDate, Carbon $endDate) {
    $agent = Agent::factory()->create();
    $agentWithoutPaidLeaves = Agent::factory()->create();

    foreach ([0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::VACATION->value,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    expect((new PaidLeaveCommissionService())->calculate($agentWithoutPaidLeaves, $timeScope))->toBe(0);
})->with([
    [
        TimeScopeEnum::MONTHLY,
        Carbon::now()->firstOfMonth()->addDays(5),
        Carbon::now()->firstOfMonth()->addDays(10),
    ],
    [
        TimeScopeEnum::QUARTERLY,
        Carbon::now()->firstOfQuarter()->addDays(5),
        Carbon::now()->firstOfQuarter()->addDays(10),
    ],
    [
        TimeScopeEnum::ANNUALY,
        Carbon::now()->firstOfYear()->addDays(5),
        Carbon::now()->firstOfYear()->addDays(10),
    ],
]);

it('returns 0 if the paid leave has no end date - for sickness', function (TimeScopeEnum $timeScope) {
    $agent = Agent::factory()->create();

    foreach ([0, 1] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => Carbon::now(),
            'end_date' => null,
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    expect((new PaidLeaveCommissionService())->calculate($agent, $timeScope))->toBe(0);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);