<?php

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Services\PaidLeaveDaysService;
use Carbon\CarbonImmutable;

it('returns correct sick days count for timescope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDayInScope) {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)
            ->state([
                'start_date' => $firstDayInScope->addWeekdays(fake()->numberBetween(1, 5)),
                'end_date' => $firstDayInScope->addWeekdays(fake()->numberBetween(6, 10)),
                'reason' => AgentStatusEnum::SICK->value,
            ]))->create();

    $sickDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, $timeScope, AgentStatusEnum::SICK);

    $this->get(route('dashboard').'?time_scope='.$timeScope->value);
    expect($agent->sickLeavesDaysCount)->toBe(count($sickDays));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()],
]);
