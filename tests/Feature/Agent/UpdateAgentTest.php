<?php

use App\Enum\AgentStatusEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;

it('can update an agent as an admin', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->put(route('agents.update', $agent), [
        'name' => $name = 'John Doe',
        'email' => $email = 'john.doe@gmail.com',
        'base_salary' => $baseSalary = 10000000,
        'on_target_earning' => $onTargetEarning = 20000000,
        'status' => $status = AgentStatusEnum::SICK->value,
    ])->assertRedirect();

    $agent->refresh();

    expect($agent->name)->toBe($name);
    expect($agent->email)->toBe($email);
    expect($agent->base_salary)->toBe($baseSalary);
    expect($agent->on_target_earning)->toBe($onTargetEarning);
    expect($agent->organization->id)->toBe($agent->organization->id);
    expect($agent->status->value)->toBe($status);
});

it('cannot update a foreign agent as an admin', function () {
    signInAdmin();

    $agent = Agent::factory()->create();

    StoreAgentRequest::fake();

    $this->put(route('agents.update', $agent))->assertForbidden();
});

it('can update an agent with casted nullable base_salary and on_target_earning', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    StoreAgentRequest::factory()->state([
        'base_salary' => 0,
        'on_target_earning' => 0,
    ])->fake();

    $this->put(route('agents.update', $agent))->assertValid()->assertRedirect();

    $agent->refresh();

    expect($agent = Agent::first())->not()->toBeNull();
    expect($agent->base_salary)->toBeNull();
    expect($agent->on_target_earning)->toBeNull();
});

it('cannot update an agent with a mail already taken by an admin', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    StoreAgentRequest::factory()->state([
        'email' => $admin->email,
    ])->fake();

    $this->put(route('agents.update', $agent))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('cannot update an agent with a mail already taken by an agent', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    StoreAgentRequest::factory()->state([
        'email' => Agent::factory()->create()->email,
    ])->fake();

    $this->put(route('agents.update', $agent))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('can update an agent with his same email', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    StoreAgentRequest::factory()->state([
        'email' => $agent->email,
    ])->fake();

    $this->put(route('agents.update', $agent))->assertValid()->assertRedirect();
});
