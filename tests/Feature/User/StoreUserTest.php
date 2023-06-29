<?php

use App\Models\User;

it('can create an agent as an organization admin', function () {
    // was mit agent?
    // request stub return kommentar?
    // salary etc in user tabelle?
    // password mitschicken?
    // directory structure?
    $user = signIn();

    $this->post(route('users.store'), [
        'name' => $name = 'John Doe',
        'email' => $email = 'john.doe@gmail.com',
        'base_salary' => $baseSalary = 10000000,
        'on_target_earning' => $onTargetEarning = 20000000,
    ])->assertRedirect();

    expect($user = User::whereName($name)->first())->not()->toBeNull();
    expect($user->email)->toBe($email);
    expect($user->base_salary)->toBe($baseSalary);
    expect($user->on_target_earning)->toBe($onTargetEarning);
});
