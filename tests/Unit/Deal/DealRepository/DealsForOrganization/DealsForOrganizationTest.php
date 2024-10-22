<?php

use App\Enum\DealScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Repositories\DealRepository;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = signInAdmin();

    Deal::factory($this->openDealCount = 2)
        ->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SCHEDULED)
        ->create();

    Deal::factory($this->acceptedDealCount = 3)
        ->withAgentDeal(Agent::factory()->create([
            'organization_id' => $this->admin->organization_id,
        ])->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
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

it('no scope returns all deals', function () {
    expect(DealRepository::dealsForOrganization($this->admin->organization)->get())->toHaveCount($this->openDealCount + $this->acceptedDealCount + $this->activeRejectedDealCount + $this->inactiveRejectedDealCount);
});

it('open scope returns non-accepted deals and deals whose rejection is not active', function () {
    expect(DealRepository::dealsForOrganization($this->admin->organization, DealScopeEnum::OPEN)->get())->toHaveCount($this->openDealCount + $this->inactiveRejectedDealCount);
});

it('accepted scope returns accepted deals', function () {
    expect(DealRepository::dealsForOrganization($this->admin->organization, DealScopeEnum::ACCEPTED)->get())->toHaveCount($this->acceptedDealCount);
});

it('rejected scope returns deals with an active rejection', function () {
    expect(DealRepository::dealsForOrganization($this->admin->organization, DealScopeEnum::REJECTED)->get())->toHaveCount($this->activeRejectedDealCount);
});
