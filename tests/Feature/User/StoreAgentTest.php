<?php

use App\Http\Requests\StoreAgentRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('can create an agent as an organization admin', function () {
    Role::create(['name' => 'agent']);
    $user = signIn();

    $this->post(route('agents.store'), [
        'name' => $name = 'John Doe',
        'email' => $email = 'john.doe@gmail.com',
        'base_salary' => $baseSalary = 10000000,
        'on_target_earning' => $onTargetEarning = 20000000,
    ])->assertRedirect();

    expect($user = User::whereName($name)->first())->not()->toBeNull();
    expect($user->email)->toBe($email);
    expect($user->base_salary)->toBe($baseSalary);
    expect($user->on_target_earning)->toBe($onTargetEarning);

    expect($user->hasRole('agent'))->toBeTrue();
});

it('can create an agent with nullable base_salary and on_target_earning', function () {
    $user = signIn();
    Role::create(['name' => $role = 'agent']);

    StoreAgentRequest::factory()->state([
        'base_salary' => null,
        'on_target_earning' => null,
    ])->fake();

    $this->post(route('agents.store'))->assertRedirect();

    expect($user = User::role($role)->first())->not()->toBeNull();
    expect($user->base_salary)->toBeNull();
    expect($user->on_target_earning)->toBeNull();
});

// felder clearen nach create
// duplicate email
