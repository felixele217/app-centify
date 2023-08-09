<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\StorePaidLeaveRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->agent = Agent::factory()->ofOrganization($this->admin->organization_id)->create();
});

it('cannot store a paid leave that overlaps with an existing paid leave', function () {
    PaidLeave::factory()->create([
        'agent_id' => $this->agent->id,
        'start_date' => $startDate = CarbonImmutable::today(),
        'end_date' => $endDate = CarbonImmutable::parse('+1 week'),
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

it('cannot store a paid leave where the end date reaches into another paid leave', function () {
    PaidLeave::factory()->create([
        'agent_id' => $this->agent->id,
        'start_date' => $startDate = CarbonImmutable::today(),
        'end_date' => $endDate = CarbonImmutable::parse('+1 week'),
    ]);

    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $startDate->subDays(1),
        'end_date' => $endDate->subDays(1),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid([
        'start_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
        'end_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
    ]);
});

it('cannot store a paid leave where the start date reaches into another paid leave', function () {
    PaidLeave::factory()->create([
        'agent_id' => $this->agent->id,
        'start_date' => $startDate = CarbonImmutable::today(),
        'end_date' => $endDate = CarbonImmutable::parse('+1 week'),
    ]);

    StorePaidLeaveRequest::factory()->state([
        'reason' => AgentStatusEnum::SICK->value,
        'start_date' => $startDate->addDays(1),
        'end_date' => $endDate->addDays(1),
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
        'sum_of_commissions' => 10_000_00,
    ])->fake();

    $this->post(route('agents.paid-leaves.store', $this->agent))->assertInvalid([
        'start_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
        'end_date' => 'You must specify a timeframe that does not overlap with existing paid leaves.',
    ]);
});
