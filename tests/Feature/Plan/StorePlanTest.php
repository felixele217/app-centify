<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
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
        'assigned_agents' => $assignedAgents = $agents->pluck('id')->toArray(),
    ])->assertRedirect(route('plans.index'));

    expect($plan = Plan::whereName($name)->first())->not()->toBeNull();
    expect($plan->start_date)->toEqual($startDate);
    expect($plan->target_amount_per_month)->toEqual($targetAmountPerMonth);
    expect($plan->target_variable->value)->toEqual($targetVariable);
    expect($plan->payout_frequency->value)->toEqual($payoutFrequency);
    expect($plan->agents->pluck('id')->toArray())->toEqual($assignedAgents);
    expect($plan->organization->id)->toEqual($user->organization->id);
});
