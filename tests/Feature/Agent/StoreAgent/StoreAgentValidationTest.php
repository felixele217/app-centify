<?php

use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;

it('has required fields', function () {
    signInAdmin();

    $this->post(route('agents.store'))->assertInvalid([
        'name' => 'The name field is required.',
        'email' => 'The email field is required.',
        'base_salary' => 'The base salary field is required.',
        'on_target_earning' => 'The on target earning field is required.',
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

it('cannot store an agent with a larger on_target_earning than base_salary', function (int $baseSalary, int $onTargetEarning) {
    signInAgent();

    StoreAgentRequest::factory()->state([
        'base_salary' => $baseSalary,
        'on_target_earning' => $onTargetEarning,
    ])->fake();

    $this->post(route('agents.store'))->assertInvalid([
        'on_target_earning' => 'The on target earning must be greater than the base salary.',
    ]);
})->with([
    [50_000_00, 30_000_00],
    [120_000_00, 75_000_00],
]);

it('does not fail when using null values in the paid leave object', function () {
   signInAgent();

    StoreAgentRequest::factory()->fake();

    $this->post(route('agents.store'))->assertRedirect();
});
