<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'creator_id' => $admin,
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach(Agent::factory($agentCount = 3)
        ->hasDeals(3)
        ->hasPaidLeaves(1, [
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
        ])
        ->create(['organization_id' => $admin->organization->id]));

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('agents', $agentCount)
                ->has('agents.1.deals')
                ->has('agents.1.active_plans')
                ->has('agents.1.quota_attainment')
                ->has('agents.1.quota_attainment_change')
                ->has('agents.1.commission')
                ->has('agents.1.commission_change')
                ->has('agents.1.sick_leaves_days_count')
                ->has('agents.1.vacation_leaves_days_count')
                ->has('agents.1.paid_leaves.0.start_date')
                ->has('agents.1.paid_leaves.0.end_date')
        );
});

it('sends correct todo count for this organization', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'creator_id' => $admin,
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($firstAgent = Agent::factory()->create(['organization_id' => $admin->organization->id]));
    $plan->agents()->attach($secondAgent = Agent::factory()->create());

    Deal::factory($openDealCount = 3)
        ->withAgentDeal($firstAgent->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    Deal::factory(3)
        ->withAgentDeal($secondAgent->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->where('open_deal_count', $openDealCount)
        );
});

it('does not send foreign agents', function () {
    signInAdmin();

    Agent::factory(5)->create();

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('agents', 0)
        );
});
