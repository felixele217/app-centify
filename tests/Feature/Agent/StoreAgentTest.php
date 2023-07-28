<?php

use App\Enum\AgentStatusEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;

it('can create an agent as an admin', function () {
    signInAdmin();

    $this->post(route('agents.store'), [
        'name' => $name = 'John Doe',
        'email' => $email = 'john.doe@gmail.com',
        'base_salary' => $baseSalary = 10000000,
        'on_target_earning' => $onTargetEarning = 20000000,
        'status' => AgentStatusEnum::ACTIVE->value,
    ])->assertRedirect();

    expect($agent = Agent::whereName($name)->first())->not()->toBeNull();
    expect($agent->email)->toBe($email);
    expect($agent->base_salary)->toBe($baseSalary);
    expect($agent->on_target_earning)->toBe($onTargetEarning);
    expect($agent->organization->id)->toBe($agent->organization->id);
});

it('has required fields', function () {
    signInAdmin();

    $this->post(route('agents.store'), [
        'base_salary' => 0,
        'on_target_earning' => 0,
    ])->assertInvalid([
        'name' => 'The name field is required.',
        'email' => 'The email field is required.',
        'base_salary' => 'The base salary field is required.',
        'on_target_earning' => 'The on target earning field is required.',
        'status' => 'The status field is required.',
    ]);
});

it('cannot create an agent with a mail already taken by an admin', function () {
    $admin = signInAdmin();

    StoreAgentRequest::factory()->state([
        'email' => $admin->email,
    ])->fake();

    $this->post(route('agents.store'))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('cannot create an agent with a mail already taken by an agent', function () {
    $agent = signInAgent();

    StoreAgentRequest::factory()->state([
        'email' => $agent->email,
    ])->fake();

    $this->post(route('agents.store'))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('does not fail when using null values in the paid leave object', function () {
    $agent = signInAgent();

    StoreAgentRequest::factory()->fake();

    $this->post(route('agents.store'))->assertRedirect();
});
