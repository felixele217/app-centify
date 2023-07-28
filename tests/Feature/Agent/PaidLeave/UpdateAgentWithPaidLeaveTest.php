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

it('can set end date to null if status is sick', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => AgentStatusEnum::SICK->value,
        'paid_leave' => [
            'start_date' => Carbon::today(),
            'end_date' => null,
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertValid();
});

it('does not create a new paid leave if the time frame already exists', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    PaidLeave::factory()->create([
        'agent_id' => $agent->id,
        'start_date' => Carbon::parse('-2 days'),
        'end_date' => Carbon::parse('+2 days'),
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => AgentStatusEnum::SICK->value,
        'paid_leave' => [
            'start_date' => $newStartDate = Carbon::today(),
            'end_date' => Carbon::parse('+1 week'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertRedirect();

    expect($agent->paidLeaves)->toHaveCount(1);
    expect($agent->activePaidLeave->start_date->toDateString())->toBe($newStartDate->toDateString());
});

it('cannot specify an end date that is before the start date', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdateAgentRequest::factory()->state([
        'status' => AgentStatusEnum::VACATION->value,
        'paid_leave' => [
            'start_date' => Carbon::parse('+1 week'),
            'end_date' => Carbon::parse('-1 week'),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 10_000_00,
        ],
    ])->fake();

    $this->put(route('agents.update', $agent->id))->assertInvalid();
});
