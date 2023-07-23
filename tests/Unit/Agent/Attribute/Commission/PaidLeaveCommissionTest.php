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

it('calculates the commission with the additional paid leave value for the current quarter', function () {
    $agent = Agent::factory()->create();

    $agent->paidLeaves()->create([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $firstPaidLeaveStartDate = Carbon::now()->firstOfQuarter()->subDay(3),
        'end_date' => $firstPaidLeaveEndDate = Carbon::now()->firstOfQuarter(),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => $sumOfCommissions = 10_000_00,
    ]);

    $agent->paidLeaves()->create([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $secondPaidLeaveStartDate = Carbon::now()->lastOfQuarter()->subDay(3),
        'end_date' => $secondPaidLeaveEndDate = Carbon::now()->lastOfQuarter(),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => $sumOfCommissions,
    ]);

    $paidLeaveDays = $firstPaidLeaveEndDate->diffInWeekdays($firstPaidLeaveStartDate) + $secondPaidLeaveEndDate->diffInWeekdays($secondPaidLeaveStartDate) + 2;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDays * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::QUARTERLY))->toBe(intval(round($paidLeaveCommission)));
});

it('calculates the commission with the additional paid leave value for the current year', function () {
    $agent = Agent::factory()->create();

    $agent->paidLeaves()->create([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $firstPaidLeaveStartDate = Carbon::now()->firstOfYear(),
        'end_date' => $firstPaidLeaveEndDate = Carbon::now()->firstOfYear()->addDays(3),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => $sumOfCommissions = 10_000_00,
    ]);

    $agent->paidLeaves()->create([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $secondPaidLeaveStartDate = Carbon::now()->lastOfYear()->subDay(3),
        'end_date' => $secondPaidLeaveEndDate = Carbon::now()->lastOfYear(),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => $sumOfCommissions,
    ]);

    $firstPaidLeaveDays = $firstPaidLeaveEndDate->diffInWeekdays($firstPaidLeaveStartDate) + 1;
    $secondPaidLeaveDays = $secondPaidLeaveEndDate->diffInWeekdays($secondPaidLeaveStartDate) + 1;

    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = ($firstPaidLeaveDays + $secondPaidLeaveDays) * $expectedCommissionsPerDay;

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(intval(round($paidLeaveCommission)));
});

it('does not use paid leaves outside of current month if scope is current month', function () {
    $agent = Agent::factory()->create();

    foreach ([0, 1, 2] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => Carbon::parse('-3 months'),
            'end_date' => Carbon::parse('-2 months'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(0);
});

it('does not use paid leaves outside of current quarter if scope is current quarter', function () {
    $agent = Agent::factory()->create();

    foreach ([0, 1, 2] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => Carbon::parse('-3 years'),
            'end_date' => Carbon::parse('-2 years'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::QUARTERLY))->toBe(0);
});

it('does not use paid leaves outside of current year if scope is current year', function () {

    $agent = Agent::factory()->create();

    foreach ([0, 1, 2] as $_) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => Carbon::parse('-5 years'),
            'end_date' => Carbon::parse('-4 years'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ]);
    }

    expect((new PaidLeaveCommissionService())->calculate($agent, TimeScopeEnum::ANNUALY))->toBe(0);
});
