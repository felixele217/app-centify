<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\StorePaidLeaveRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

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
