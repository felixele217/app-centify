<?php

use App\Enum\DealScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Repositories\DealRepository;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->admin = signInAdmin();

    Deal::factory($this->openDealCount = 2)
        ->withAgentOfOrganization($this->admin->organization->id)
        ->create();

    Deal::factory($this->acceptedDealCount = 3)
        ->accepted()
        ->withAgentOfOrganization($this->admin->organization->id)
        ->create();

    Deal::factory($dealsWithImpermanentRejectionThisMonth = 2)
        ->hasRejections(1, [
            'created_at' => Carbon::now()->firstOfMonth(),
            'is_permanent' => false,
        ])->withAgentOfOrganization($this->admin->organization->id)
        ->create();

    Deal::factory($dealsWithPermanentRejectionLastMonth = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => true,
    ])->withAgentOfOrganization($this->admin->organization->id)
        ->create();

    Deal::factory($this->inactiveRejectedDealCount = 2)->hasRejections(1, [
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => false,
    ])->withAgentOfOrganization($this->admin->organization->id)
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
            ->has('deals.1.agent')
            ->has('deals.1.splits')
            ->has('agents')
            ->has('deals.1.active_rejection')
            ->has('integrations')
    );
});

it('passes the correct props for all deals if no scope is specified', function () {
    $this->get(route('deals.index'))
    ->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get()->count())
            ->has('deals.1.agent')
    );
});

it('passes the correct props for scope=open', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::OPEN->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::OPEN)->count())
            ->has('deals.1.agent')
    );
});

it('passes the correct props for scope=accepted', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::ACCEPTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::ACCEPTED)->count())
            ->has('deals.1.agent')
    );
});

it('passes the correct props for scope=rejected', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::REJECTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', DealRepository::get(DealScopeEnum::REJECTED)->count())
            ->has('deals.1.agent')
    );
});
