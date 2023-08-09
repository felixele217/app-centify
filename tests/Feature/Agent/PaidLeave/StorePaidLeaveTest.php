<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\StorePaidLeaveRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->agent = Agent::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]);
});

it('stores the paid leave when an agent is stored on vacation or sick', function (string $status) {
    $this->post(route('agents.paid-leaves.store', $this->agent), [
        'reason' => $status,
        'start_date' => $startDate = Carbon::today(),
        'end_date' => $endDate = Carbon::parse('+1 week'),
        'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        'employed_28_or_more_days' => true,
    ])->assertRedirect();

    $paidLeave = PaidLeave::whereAgentId($this->agent->id)->first();

    expect($paidLeave)->not()->toBeNull();
    expect($paidLeave->start_date->toDateString())->toBe($startDate->toDateString());
    expect($paidLeave->end_date->toDateString())->toBe($endDate->toDateString());
    expect($paidLeave->continuation_of_pay_time_scope->value)->toBe($continuationOfPayTimeScope);
    expect($paidLeave->sum_of_commissions)->toBe($sumOfCommissions);
})->with([
    AgentStatusEnum::VACATION->value,
    AgentStatusEnum::SICK->value,
]);

it('requires an end date for a vacation', function () {
    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::VACATION->value,
        'start_date' => null,
        'end_date' => null,
        'continuation_of_pay_time_scope' => null,
        'sum_of_commissions' => null,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid([
        'start_date',
        'end_date',
        'continuation_of_pay_time_scope',
        'sum_of_commissions',
    ]);
});

it('can set end date to null if status is sick', function () {
    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => Carbon::today(),
        'end_date' => null,
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertValid();
});

it('throws a validation error if status is sick and employed 28 or more days is false', function () {
    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'employed_28_or_more_days' => false,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid([
        'employed_28_or_more_days' => 'The employee has to be employed for 28 or more days.',
    ]);
});

it('cannot specify an end date that is before the start date', function () {
    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => Carbon::parse('+1 week'),
        'end_date' => Carbon::parse('-1 week'),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid();
});

it('cannot store a paid leave that overlaps with an existing paid leave', function () {
    PaidLeave::factory()->create([
        'agent_id' => $this->agent->id,
        'start_date' => $startDate = CarbonImmutable::today(),
        'end_date' => $endDate = CarbonImmutable::parse(),
    ]);

    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $startDate->subDays(1),
        'end_date' => $endDate->addDays(1),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid([
        'start_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
        'end_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
    ]);
});

it('can store a paid leave that does not overlap with existing paid leaves', function (int $startDateAddedDays, int $endDateAddedDays) {
    PaidLeave::factory()->create([
        'agent_id' => $this->agent->id,
        'start_date' => $startDate = CarbonImmutable::today(),
        'end_date' => $startDate->addDays(1),
    ]);

    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $startDate->addDays($startDateAddedDays),
        'end_date' => $startDate->addDays($endDateAddedDays),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertValid();
})->with([
    [3, 5],
    [-5, -3],
]);
