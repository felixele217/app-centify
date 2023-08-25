<?php

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
    ])->assertRedirect();

    $agent->refresh();

    expect($agent->name)->toBe($name);
    expect($agent->email)->toBe($email);
    expect($agent->base_salary)->toBe($baseSalary);
    expect($agent->on_target_earning)->toBe($onTargetEarning);
    expect($agent->organization->id)->toBe($agent->organization->id);
});
