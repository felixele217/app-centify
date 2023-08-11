<?php

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Services\PaidLeaveDaysService;
use Carbon\CarbonImmutable;

it('returns correct vacation days count for timescope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDayInScope) {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)
            ->state([
                'start_date' => $firstDayInScope->addWeekdays(fake()->numberBetween(1, 5)),
                'end_date' => $firstDayInScope->addWeekdays(fake()->numberBetween(6, 10)),
                'reason' => AgentStatusEnum::VACATION->value,
            ]))->create();

    $vacationDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, $timeScope, AgentStatusEnum::VACATION);

    $this->get(route('dashboard').'?time_scope='.$timeScope->value);
    expect($agent->vacationLeavesDaysCount)->toBe(count($vacationDays));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()],
]);
