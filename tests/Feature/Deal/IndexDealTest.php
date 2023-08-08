<?php

use App\Enum\DealScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->admin = signInAdmin();

    Deal::factory($this->openDealCount = 2)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
    ]);

    Deal::factory($this->acceptedDealCount = 3)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
        'accepted_at' => Carbon::now(),
    ]);

    Deal::factory($this->declinedDealCount = 4)->hasRejections(1)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
    ]);

    Deal::factory($this->foreignDealCount = 1)->create();
});

it('passes the correct props for all deals if no scope is specified', function () {
    $this->get(route('deals.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->openDealCount + $this->acceptedDealCount + $this->declinedDealCount)
            ->has('deals.1.agent')
            ->has('deals.1.active_rejection')
            ->has('integrations')
    );
});

it('passes the correct props for scope=open', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::OPEN->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->openDealCount)
            ->has('deals.1.agent')
    );
});

it('passes the correct props for scope=accepted', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::ACCEPTED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->acceptedDealCount)
            ->has('deals.1.agent')
    );
});

it('passes the correct props for scope=declined', function () {
    $this->get(route('deals.index').'?scope='.DealScopeEnum::DECLINED->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->declinedDealCount)
            ->has('deals.1.agent')
    );
});
