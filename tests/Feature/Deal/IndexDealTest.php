<?php

use App\Enum\DealScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Integration;
use App\Repositories\DealRepository;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->admin = signInAdmin();

    Integration::factory()->hasCustomFields()->create([
        'organization_id' => $this->admin->organization->id,
    ]);

    Deal::factory($this->openDealCount = 2)
        ->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    Deal::factory($this->acceptedDealCount = 3)
        ->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SET_BY, Carbon::now()->firstOfMonth())
        ->create();

    Deal::factory($dealsWithImpermanentRejectionThisMonth = 2)
        ->hasRejections(1, [
            'created_at' => Carbon::now()->firstOfMonth(),
            'is_permanent' => false,
        ])->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    Deal::factory($dealsWithPermanentRejectionLastMonth = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => true,
    ])->withAgentDeal(Agent::factory()->create([
        'organization_id' => $this->admin->organization_id,
    ])->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    Deal::factory($this->inactiveRejectedDealCount = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => false,
    ])->withAgentDeal(Agent::factory()->create([
        'organization_id' => $this->admin->organization_id,
    ])->id, TriggerEnum::DEMO_SET_BY)
        ->create();

    $this->activeRejectedDealCount = $dealsWithImpermanentRejectionThisMonth + $dealsWithPermanentRejectionLastMonth;

    Deal::factory($this->foreignDealCount = 1)->create();
});

it('passes the correct props', function () {
    $this->get(route('deals.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Deal/Index')
                ->has('deals', DealRepository::get()->count())
                ->has('deals.1.agents')
                ->has('deals.1.s_d_r')
                ->has('deals.1.a_e')
                ->has('agents')
                ->has('deals.1.active_rejection')
                ->has('integrations.0.custom_fields')
        );
});

it('passes the correct props for all deals if no scope is specified', function () {
    $this->get(route('deals.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Deal/Index')
                ->has('deals', DealRepository::get()->count())
                ->has('deals.1.agents')
        );
});

it('passes the correct props for scope=open', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::OPEN->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::OPEN)->count())
            ->has('deals.1.agents')
    );
});

it('passes the correct props for scope=accepted', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::ACCEPTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::ACCEPTED)->count())
            ->has('deals.1.agents')
    );
});

it('passes the correct props for scope=rejected', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::REJECTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::REJECTED)->count())
            ->has('deals.1.agents')
    );
});
