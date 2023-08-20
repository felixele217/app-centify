<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $agents = Agent::factory(3)->hasPaidLeaves(1)->create([
        'organization_id' => $admin->organization->id,
    ]);

    Plan::factory($planCount = 3)->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->get(route('agents.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Agent/Index')
                ->has('agents', $agents->count())
                ->has('agents.0.active_paid_leave')
                ->has('agents.0.active_plans')
                ->has('plans', $planCount, fn (AssertableInertia $page) => $page
                    ->has('id')
                    ->has('name')
                )
                ->where('possible_statuses', array_column(AgentStatusEnum::cases(), 'value'))
                ->where('continuation_of_pay_time_scope_options', array_column(ContinuationOfPayTimeScopeEnum::cases(), 'value'))
        );
});

it('does not send foreign props', function () {
    signInAdmin();

    Agent::factory(3)->create();

    Plan::factory(3)->create();

    $this->get(route('agents.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Agent/Index')
                ->has('agents', 0)
                ->has('plans', 0)
        );
});
