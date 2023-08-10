<?php

use App\Models\Agent;
use App\Models\PaidLeave;

it('can destroy a paid leave', function () {
    $admin = signInAdmin();

    $paidLeave = PaidLeave::factory()->create([
        'agent_id' => $agent = Agent::factory()->ofOrganization($admin->organization_id)->create(),
    ]);

    $this->delete(route('agents.paid-leaves.destroy', [$agent, $paidLeave]))->assertRedirect();

    expect($paidLeave->fresh())->toBeNull();
});
