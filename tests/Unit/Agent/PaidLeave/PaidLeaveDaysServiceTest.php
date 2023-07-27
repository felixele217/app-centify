<?php

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Services\PaidLeaveDaysService;
use Carbon\Carbon;

it('returns an array of the sick leave days', function () {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)->sequence([
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(2),
        ], [
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(3),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(5),
        ])->state([
            'reason' => AgentStatusEnum::SICK->value,
        ]))->create();

    $sickDays = (new PaidLeaveDaysService())->sickDays($agent, TimeScopeEnum::MONTHLY);

    expect(count($sickDays))->toBe(5);

    foreach ($sickDays as $index => $day) {
        expect($day->toDateString())->toBe(Carbon::now()->firstOfMonth()->addWeekdays($index + 1)->toDateString());
    }
});
