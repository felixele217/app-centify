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

    $sickDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, TimeScopeEnum::MONTHLY, AgentStatusEnum::SICK);

    expect(count($sickDays))->toBe(5);

    foreach ($sickDays as $index => $day) {
        expect($day->toDateString())->toBe(Carbon::now()->firstOfMonth()->addWeekdays($index + 1)->toDateString());
    }
});

it('does not return vacation days with the sick leave days', function () {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)->sequence([
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(2),
            'reason' => AgentStatusEnum::VACATION->value,
        ]))->create();

    $sickDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, TimeScopeEnum::MONTHLY, AgentStatusEnum::SICK);

    expect(count($sickDays))->toBe(0);
});

it('returns an array of the vacation leave days', function () {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)->sequence([
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(2),
        ], [
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(3),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(5),
        ])->state([
            'reason' => AgentStatusEnum::VACATION->value,
        ]))->create();

    $sickDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, TimeScopeEnum::MONTHLY, AgentStatusEnum::VACATION);

    expect(count($sickDays))->toBe(5);

    foreach ($sickDays as $index => $day) {
        expect($day->toDateString())->toBe(Carbon::now()->firstOfMonth()->addWeekdays($index + 1)->toDateString());
    }
});

it('does not return sick days with the vacation leave days', function () {
    $agent = Agent::factory()->has(
        PaidLeave::factory()->count(2)->sequence([
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(2),
            'reason' => AgentStatusEnum::SICK->value,
        ]))->create();

    $sickDays = (new PaidLeaveDaysService())->paidLeaveDays($agent, TimeScopeEnum::MONTHLY, AgentStatusEnum::VACATION);

    expect(count($sickDays))->toBe(0);
});
