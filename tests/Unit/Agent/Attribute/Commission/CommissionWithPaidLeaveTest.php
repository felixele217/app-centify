<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('calculates the commission with the additional paid leave value for the current month', function () {
    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now()->firstOfMonth(),
        'value' => 1_000_00,
    ])->create([
        'base_salary' => 80_000_00,
        'on_target_earning' => 200_000_00,
    ]));

    foreach ([0, 1] as $index) {
        $agent->paidLeaves()->create([
            'reason' => AgentStatusEnum::SICK->value,
            'start_date' => $paidLeaveStartDate = Carbon::parse('-10 days'),
            'end_date' => $paidLeaveEndDate = Carbon::parse('-2 days'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ]);
    }

    $paidLeaveDays = $paidLeaveEndDate->diffInDays($paidLeaveStartDate) + 1;
    $expectedCommissionsPerDay = $sumOfCommissions / 90;

    $paidLeaveCommission = $paidLeaveDays * $expectedCommissionsPerDay;

    expect($agent->commission)->toBe(intval(round(20_000_00 + $paidLeaveCommission * 2)));
});
