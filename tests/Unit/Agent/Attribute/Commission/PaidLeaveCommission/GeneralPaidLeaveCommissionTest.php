<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;

it('calculates the paid leave commission just for the employee with the paid leaves', function (TimeScopeEnum $timeScope, Carbon $startDate, Carbon $endDate) {
    $agent = Agent::factory()->create([
        'name' => 'with leaves',
    ]);
    $agentWithoutPaidLeaves = Agent::factory()->create([
        'name' => 'without leaves',
    ]);

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
