<?php

use App\Enum\PlanCycleEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TriggerEnum;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Agent;
use App\Models\AgentPlan;
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
        'plan_cycle' => $planCycle = fake()->randomElement(PlanCycleEnum::cases())->value,
        'assigned_agents' => [],
        'trigger' => fake()->randomElement(TriggerEnum::cases())->value,
    ])->assertRedirect(route('plans.index'));

    $plan->refresh();

    expect($plan->name)->toBe($name);
    expect($plan->start_date->toDateString())->toBe($startDate->toDateString());
    expect($plan->target_amount_per_month)->toBe($targetAmountPerMonth);
    expect($plan->target_variable->value)->toBe($targetVariable);
    expect($plan->plan_cycle->value)->toBe($planCycle);
});

it('can assign more agents to the plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $agentPlans = AgentPlan::factory(2)->create([
        'plan_id' => $plan->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'assigned_agents' => $newAgents = [
            ...Agent::factory(3)->create()->map(fn (Agent $agent) => [
                'id' => $agent->id,
                'share_of_variable_pay' => 100,
            ])->toArray(),
            ...$agentPlans->map(fn (AgentPlan $agentPlan) => [
                'id' => $agentPlan->agent->id,
                'share_of_variable_pay' => 100,
            ])->toArray(),
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(count($newAgents));
});

it('can remove agents from the plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agents = Agent::factory(4)->create());

    UpdatePlanRequest::factory()->state([
        'assigned_agents' => $agents->take($expectedAgentCount = 1)->map(fn (Agent $agent) => [
            'id' => $agent->id,
            'share_of_variable_pay' => 100,
        ])->toArray(),
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe($expectedAgentCount);
});

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
        'plan_cycle' => 'The plan cycle field is required.',
        'assigned_agents' => 'The assigned agents field must be present',
    ]);
});

it('cannot update a foreign plan as an admin', function () {
    signInAdmin();

    $plan = Plan::factory()->create();

    UpdatePlanRequest::fake();

    $this->put(route('plans.update', $plan))->assertForbidden();
});

it('cannot update a plan with assigned agents with a share of variable smaller than 1', function (int $shareOfVariablePay) {
    signInAdmin();

    $plan = Plan::factory()->create();

    UpdatePlanRequest::factory()->state([
        'assigned_agents' => [[
            'id' => Agent::factory()->create()->id,
            'share_of_variable_pay' => $shareOfVariablePay,
        ]],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertInvalid([
        'assigned_agents.0.share_of_variable_pay' => 'The share of variable pay of all assigned agents must be greater than 0.',
    ]);
})->with([0, -1]);
