<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

it('stores the paid leave when an agent is stored on vacation or sick', function (string $status) {
    signInAdmin();

    StoreAgentRequest::factory()->state([
        'name' => $name = 'Felix Doe',
        'status' => $status,
        'paid_leave' => [
            'start_date' => $startDate = Carbon::today(),
            'end_date' => $endDate = Carbon::parse('+1 week'),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ],
    ])->fake();

    $this->post(route('agents.store'))->assertRedirect();

    $paidLeave = PaidLeave::whereAgentId(Agent::whereName($name)->first()->id)->first();

    expect($paidLeave)->not()->toBeNull();
    expect($paidLeave->start_date->toDateString())->toBe($startDate->toDateString());
    expect($paidLeave->end_date->toDateString())->toBe($endDate->toDateString());
    expect($paidLeave->continuation_of_pay_time_scope->value)->toBe($continuationOfPayTimeScope);
    expect($paidLeave->sum_of_commissions)->toBe($sumOfCommissions);
})->with([
    AgentStatusEnum::VACATION->value,
    AgentStatusEnum::SICK->value,
]);

it('does requires an end date for a vacation', function () {
    signInAdmin();

    StoreAgentRequest::factory()->state([
        'status' => AgentStatusEnum::VACATION->value,
        'paid_leave' => [
            'start_date' => Carbon::today(),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->post(route('agents.store'))->assertInvalid([
        'paid_leave.end_date',
    ]);
});

it('can set end date to null if status is sick', function () {
    signInAdmin();

    StoreAgentRequest::factory()->state([
        'status' => AgentStatusEnum::SICK->value,
        'paid_leave' => [
            'start_date' => Carbon::today(),
            'end_date' => null,
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->post(route('agents.store'))->assertValid();
});

it ('cannot specify an end date that is before the start date', function () {
    signInAdmin();

    StoreAgentRequest::factory()->state([
        'status' => AgentStatusEnum::VACATION->value,
        'paid_leave' => [
            'start_date' => Carbon::parse('+1 week'),
            'end_date' => Carbon::parse('-1 week'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->post(route('agents.store'))->assertInvalid();
});
