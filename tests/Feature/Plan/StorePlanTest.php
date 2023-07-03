<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

it('can store a plan as an admin', function () {
    $user = signIn();

    $agents = User::factory(3)->agent()->create();

    $this->post(route('plans.store'), [
        'name' => $name = 'first commission plan',
        'start_date' => $startDate = Carbon::tomorrow(),
        'target_amount_per_month' => $targetAmountPerMonth = 500000,
        'target_variable' => $targetVariable = TargetVariableEnum::ARR->value,
        'payout_frequency' => $payoutFrequency = PayoutFrequencyEnum::MONTHLY->value,
        'assigned_agent_ids' => $assignedAgents = $agents->pluck('id')->toArray(),
    ])->assertRedirect(route('plans.index'));

    expect($plan = Plan::whereName($name)->first())->not()->toBeNull();
    expect($plan->start_date)->toEqual($startDate);
    expect($plan->target_amount_per_month)->toEqual($targetAmountPerMonth);
    expect($plan->target_variable->value)->toEqual($targetVariable);
    expect($plan->payout_frequency->value)->toEqual($payoutFrequency);
    expect($plan->agents->pluck('id')->toArray())->toEqual($assignedAgents);
    expect($plan->organization->id)->toEqual($user->organization->id);
});

it('throws a validation error if target_amount_per_month is smaller than 1', function ($targetAmountPerMonth) {
    signIn();

    StorePlanRequest::factory()->state([
        'target_amount_per_month' => $targetAmountPerMonth,
    ])->fake();

    $this->post(route('plans.store'))->assertInvalid([
        'target_amount_per_month' => 'The target amount per month must be at least 0,01€.',
    ]);
})->with([
    0,
    -1,
]);

it ('transforms start_date to a valid carbon object', function () {
     signIn();

    StorePlanRequest::factory()->state([
        'name' => $name = 'some name',
        'start_date' => '2023-07-12T22:00:00.000Z',
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect($plan = Plan::whereName($name)->first())->not()->toBeNull();
})->skip();
