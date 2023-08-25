<?php

use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;

it('cannot update a foreign agent as an admin', function () {
    signInAdmin();

    $agent = Agent::factory()->create();

    UpdateAgentRequest::fake();

    $this->put(route('agents.update', $agent))->assertForbidden();
});
