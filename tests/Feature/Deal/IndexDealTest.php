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
        ])->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();

    Deal::factory($this->acceptedDealCount = 3)
        ->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now()->firstOfMonth())
        ->create();

    Deal::factory($dealsWithImpermanentRejectionThisMonth = 2)
        ->hasRejections(1, [
            'created_at' => Carbon::now()->firstOfMonth(),
            'is_permanent' => false,
        ])->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();

    Deal::factory($dealsWithPermanentRejectionLastMonth = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => true,
    ])->withAgentDeal(Agent::factory()->create([
        'organization_id' => $this->admin->organization_id,
    ])->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();

    Deal::factory($this->inactiveRejectedDealCount = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => false,
    ])->withAgentDeal(Agent::factory()->create([
        'organization_id' => $this->admin->organization_id,
    ])->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();

    $this->activeRejectedDealCount = $dealsWithImpermanentRejectionThisMonth + $dealsWithPermanentRejectionLastMonth;

    Deal::factory($this->foreignDealCount = 1)->create();
});

it('passes the correct props', function () {
    $this->get(route('deals.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Deal/Index')
                ->has('deals', DealRepository::dealsForOrganization($this->admin->organization)->count())
                ->has('deals.0.agents')
                ->has('deals.0.s_d_r')
                ->has('deals.0.a_e')
                ->has('deals.0.demo_scheduled_shareholders')
                ->has('deals.0.deal_won_shareholders')
                ->has('deals.0.active_rejection')
                ->has('integrations.0.custom_fields')
        );
});

it('passes the correct deals if no scope is specified', function () {
    $this->get(route('deals.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Deal/Index')
                ->has('deals', DealRepository::dealsForOrganization($this->admin->organization)->count())
        );
});

it('passes the correct deals for scope=open', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::OPEN->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::dealsForOrganization($this->admin->organization,DealScopeEnum::OPEN)->count())
    );
});

it('passes the correct deals for scope=accepted', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::ACCEPTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::dealsForOrganization($this->admin->organization,DealScopeEnum::ACCEPTED)->count())
    );
});

it('passes the correct deals for scope=rejected', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::REJECTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::dealsForOrganization($this->admin->organization,DealScopeEnum::REJECTED)->count())
    );
});
