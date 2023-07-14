<?php

use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;

it('can delete an agent as an admin', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    expect(Agent::count())->toBe(1);

    $this->delete(route('agents.destroy', $agent))->assertRedirect();

    expect(Agent::count())->toBe(0);
});

it('cannot delete an agent as a foreign admin', function () {
    signInAdmin();

    $agent = Agent::factory()->create();

    expect(Agent::count())->toBe(1);

    $this->delete(route('agents.destroy', $agent))->assertForbidden();
});
