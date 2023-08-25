<?php

use App\Models\Agent;

it('can create an agent as an admin', function () {
    signInAdmin();

    $this->post(route('agents.store'), [
        'name' => $name = 'John Doe',
        'email' => $email = 'john.doe@gmail.com',
        'base_salary' => $baseSalary = 10000000,
        'on_target_earning' => $onTargetEarning = 20000000,
    ])->assertRedirect();

    expect($agent = Agent::whereName($name)->first())->not()->toBeNull();
    expect($agent->email)->toBe($email);
    expect($agent->base_salary)->toBe($baseSalary);
    expect($agent->on_target_earning)->toBe($onTargetEarning);
    expect($agent->organization->id)->toBe($agent->organization->id);
});
