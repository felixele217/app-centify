<?php

use App\Enum\PlanCycleEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TriggerEnum;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Agent;
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
        'assigned_agent_ids' => [],
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

    $plan->agents()->attach($agents = Agent::factory(2)->create());

    UpdatePlanRequest::factory()->state([
        'assigned_agent_ids' => $newAgents = [
            ...Agent::factory(3)->create()->pluck('id')->toArray(),
            ...$agents->pluck('id')->toArray(),
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
        'assigned_agent_ids' => $agents->take($expectedAgentCount = 1)->pluck('id')->toArray(),
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
        'assigned_agent_ids' => 'The assigned agent ids field must be present',
    ]);
});

it('cannot update a foreign plan as an admin', function () {
    signInAdmin();

    $plan = Plan::factory()->create();

    UpdatePlanRequest::fake();

    $this->put(route('plans.update', $plan))->assertForbidden();
});
