<?php

use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    Deal::factory($dealCount = 10)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $admin->organization->id,
        ]),
    ]);

    $this->get(route('todos.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Todo/Index')
            ->has('deals', $dealCount)
            ->has('deals.1.agent')
    );
});

it('does not send foreign deals', function () {
    signInAdmin();

    Deal::factory(5)->create();

    $this->get(route('todos.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Todo/Index')
            ->has('deals', 0)
    );
});

it('only sends not accepted deals', function () {
    $admin = signInAdmin();

    Deal::factory(4)->create([
        'agent_id' => $agent = Agent::factory()->create([
            'organization_id' => $admin->organization->id,
        ]),
        'accepted_at' => Carbon::now(),
    ]);

    Deal::factory($notAcceptedDealsCount = 2)->create([
        'agent_id' => $agent->id,
        'accepted_at' => null,
    ]);

    $this->get(route('todos.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Todo/Index')
            ->has('deals', $notAcceptedDealsCount)
    );
});
