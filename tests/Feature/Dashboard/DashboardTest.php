<?php

use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
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
                ->where('time_scopes', array_column(TimeScopeEnum::cases(), 'value'))
                ->where('continuation_of_pay_time_scope_options', array_column(ContinuationOfPayTimeScopeEnum::cases(), 'value'))
        );
});

it('sends correct todo count for this organization', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'creator_id' => $admin,
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach(Agent::factory()->hasDeals(3)->create(['organization_id' => $admin->organization->id]));
    $plan->agents()->attach(Agent::factory()->hasDeals(3)->create());

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->where('open_deal_count', Deal::whereNull('accepted_at')->whereHas('agent', function (Builder $query) use ($admin) {
                    $query->whereOrganizationId($admin->organization->id);
                })->count())
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
