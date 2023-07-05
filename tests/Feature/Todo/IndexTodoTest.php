<?php

use App\Models\Agent;
use App\Models\Deal;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    Deal::factory($dealCount = 10)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $admin->organization->id,
        ]),
    ]);

    Deal::factory(5)->create();

    $this->get(route('todos.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Todo/Index')
            ->has('deals', $dealCount)
            ->has('deals.1.agent')
    );
});
