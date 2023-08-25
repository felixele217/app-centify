<?php

use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;

beforeEach(function () {
    $this->admin = signInAdmin();
    $this->agent = Agent::factory()->create([
        'organization_id' => $this->admin->organization_id,
    ]);
});

it('has required fields', function () {
    $this->put(route('agents.update', $this->agent))->assertInvalid([
        'name' => 'The name field is required.',
        'email' => 'The email field is required.',
        'base_salary' => 'The base salary field is required.',
        'on_target_earning' => 'The on target earning field is required.',
    ]);
});

it('cannot update an agent with a mail already taken by an admin', function () {
    UpdateAgentRequest::factory()->state([
        'email' => $this->admin->email,
    ])->fake();

    $this->put(route('agents.update', $this->agent))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('cannot update an agent with a mail already taken by an agent', function () {
    UpdateAgentRequest::factory()->state([
        'email' => Agent::factory()->create()->email,
    ])->fake();

    $this->put(route('agents.update', $this->agent))->assertInvalid([
        'email' => 'The email has already been taken.',
    ]);
});

it('can update an agent with his same email', function () {
    UpdateAgentRequest::factory()->state([
        'email' => $this->agent->email,
    ])->fake();

    $this->put(route('agents.update', $this->agent))->assertValid()->assertRedirect();
});

it('cannot update an agent with a larger on_target_earning than base_salary', function (int $baseSalary, int $onTargetEarning) {
    UpdateAgentRequest::factory()->state([
        'base_salary' => $baseSalary,
        'on_target_earning' => $onTargetEarning,
    ])->fake();

    $this->put(route('agents.update', $this->agent))->assertInvalid([
        'on_target_earning' => 'The on target earning must be greater than the base salary.',
    ]);
})->with([
    [50_000_00, 30_000_00],
    [120_000_00, 75_000_00],
]);

it('does not fail when using null values in the paid leave object', function () {
    UpdateAgentRequest::factory()->fake();

    $this->put(route('agents.update', $this->agent))->assertValid()->assertRedirect();
});
