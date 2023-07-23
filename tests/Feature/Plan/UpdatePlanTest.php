<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;
use Carbon\Carbon;

it('can update a plan as an admin', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->put(route('plans.update', $plan), [
        'name' => $name = 'John Doe',
        'start_date' => $startDate = Carbon::tomorrow(),
        'target_amount_per_month' => $targetAmountPerMonth = fake()->randomElement([200000, 400000]),
        'target_variable' => $targetVariable = fake()->randomElement(TargetVariableEnum::cases())->value,
        'payout_frequency' => $payoutFrequency = fake()->randomElement(PayoutFrequencyEnum::cases())->value,
    ])->assertRedirect(route('plans.index'));

    $plan->refresh();

    expect($plan->name)->toBe($name);
    expect($plan->start_date->toDateString())->toBe($startDate->toDateString());
    expect($plan->target_amount_per_month)->toBe($targetAmountPerMonth);
    expect($plan->target_variable->value)->toBe($targetVariable);
    expect($plan->payout_frequency->value)->toBe($payoutFrequency);
});

it('can assign more agents to the plan', function () {

})->todo();

it('can remove agents from the plan', function () {

})->todo();

it('has required fields', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->put(route('plans.update', $plan), [])->assertInvalid([
        'name' => 'The name field is required.',
        'start_date' => 'The start date field is required.',
        'target_amount_per_month' => 'The target amount per month field is required.',
        'target_variable' => 'The target variable field is required.',
        'payout_frequency' => 'The payout frequency field is required.',
    ]);
});

it('cannot update a foreign plan as an admin', function () {
    signInAdmin();

    $plan = Plan::factory()->create();

    StorePlanRequest::fake();

    $this->put(route('plans.update', $plan))->assertForbidden();
});
