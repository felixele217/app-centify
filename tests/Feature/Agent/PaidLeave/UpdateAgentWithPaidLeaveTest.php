<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

it('updates the paid leave when an agent is updated on vacation or sick', function (string $status) {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => $status,
        'paid_leave' => [
            'start_date' => $startDate = Carbon::today(),
            'end_date' => $endDate = Carbon::parse('+1 week'),
            'continuation_of_pay_time_scope' => $continuationOfPayTimeScope = ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => $sumOfCommissions = 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertRedirect();

    $paidLeave = PaidLeave::whereAgentId($agent->id)->first();

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
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => AgentStatusEnum::VACATION->value,
        'paid_leave' => [
            'start_date' => Carbon::today(),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertInvalid([
        'paid_leave.end_date',
    ]);
});

it('does not require an end date for a sickness', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => AgentStatusEnum::SICK->value,
        'paid_leave' => [
            'start_date' => Carbon::today(),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertValid();
});
