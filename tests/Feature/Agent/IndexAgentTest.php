<?php

use App\Enum\AgentStatusEnum;
use App\Models\Agent;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    signInAdmin();

    $agents = Agent::factory(3)->hasPaidLeaves()->create();

    $this->get(route('agents.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Agent/Index')
                ->has('agents', $agents->count())
                ->has('agents.0.active_paid_leave')
                ->where('possible_statuses', array_column(AgentStatusEnum::cases(), 'value'))
        );
});
