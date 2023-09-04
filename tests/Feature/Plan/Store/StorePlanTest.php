<?php

use App\Enum\PlanCycleEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TriggerEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('can store a plan as an admin', function () {
    $admin = signInAdmin();

    $agents = Agent::factory(3)->create();

    $this->post(route('plans.store'), [
        'name' => $name = 'first commission plan',
        'start_date' => $startDate = Carbon::tomorrow()->endOfDay(),
        'target_amount_per_month' => $targetAmountPerMonth = 500000,
        'target_variable' => $targetVariable = TargetVariableEnum::DEAL_VALUE->value,
        'plan_cycle' => $planCycle = PlanCycleEnum::MONTHLY->value,
        'assigned_agents' => $assignedAgents = $agents->map(fn (Agent $agent) => [
            'id' => $agent->id,
            'share_of_variable_pay' => 100,
        ])->toArray(),
        'trigger' => fake()->randomElement(TriggerEnum::cases())->value,
    ])->assertRedirect(route('plans.index'));

    expect(Plan::count())->toBe(1);
    expect($plan = Plan::whereName($name)->first())->not()->toBeNull();
    expect($plan->start_date->toDateString())->toEqual($startDate->toDateString());
    expect($plan->start_date->format('H'))->toEqual('00');
    expect($plan->start_date->format('i'))->toEqual('00');
    expect($plan->start_date->format('s'))->toEqual('00');
    expect($plan->target_amount_per_month)->toEqual($targetAmountPerMonth);
    expect($plan->target_variable->value)->toEqual($targetVariable);
    expect($plan->plan_cycle->value)->toEqual($planCycle);
    expect($plan->agents->pluck('id')->toArray())->toEqual(array_column($assignedAgents, 'id'));
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
        'plan_cycle' => 'The plan cycle field is required.',
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

it('cannot store a plan with assigned agents with a share of variable smaller than 1', function (int $shareOfVariablePay) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'assigned_agents' => [[
            'id' => Agent::factory()->create()->id,
            'share_of_variable_pay' => $shareOfVariablePay,
        ]],
    ])->fake();

    $this->post(route('plans.store'))->assertInvalid([
        'assigned_agents.0.share_of_variable_pay' => 'The share of variable pay of all assigned agents must be greater than 0.',
    ]);
})->with([0, -1]);
