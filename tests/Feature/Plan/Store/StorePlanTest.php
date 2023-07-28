<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('can store a plan as an admin', function () {
    $admin = signInAdmin();

    $agents = Agent::factory(3)->create();

    $this->post(route('plans.store'), [
        'name' => $name = 'first commission plan',
        'start_date' => $startDate = Carbon::tomorrow(),
        'target_amount_per_month' => $targetAmountPerMonth = 500000,
        'target_variable' => $targetVariable = TargetVariableEnum::DEAL_VALUE->value,
        'payout_frequency' => $payoutFrequency = PayoutFrequencyEnum::MONTHLY->value,
        'assigned_agent_ids' => $assignedAgents = $agents->pluck('id')->toArray(),
    ])->assertRedirect(route('plans.index'));

    expect(Plan::count())->toBe(1);
    expect($plan = Plan::whereName($name)->first())->not()->toBeNull();
    expect($plan->start_date)->toEqual($startDate);
    expect($plan->target_amount_per_month)->toEqual($targetAmountPerMonth);
    expect($plan->target_variable->value)->toEqual($targetVariable);
    expect($plan->payout_frequency->value)->toEqual($payoutFrequency);
    expect($plan->agents->pluck('id')->toArray())->toEqual($assignedAgents);
    expect($plan->creator_id)->toEqual($admin->id);
    expect($plan->organization->id)->toEqual($admin->organization->id);
});

it('has required fields', function () {
    signInAdmin();

    $this->post(route('plans.store'), [])->assertInvalid([
        'name' => 'The name field is required.',
        'start_date' => 'The start date field is required.',
        'target_amount_per_month' => 'The target amount per month field is required.',
        'target_variable' => 'The target variable field is required.',
        'payout_frequency' => 'The payout frequency field is required.',
    ]);
});

it('throws a validation error if target_amount_per_month is smaller than 1', function ($targetAmountPerMonth) {
    signInAdmin();

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
