<?php

use App\Enum\DealScopeEnum;
use App\Models\Deal;
use App\Repositories\DealRepository;
use Carbon\Carbon;

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

it('no scope returns all deals', function () {
    expect(DealRepository::get())->toHaveCount($this->openDealCount + $this->acceptedDealCount + $this->activeRejectedDealCount + $this->inactiveRejectedDealCount);
});

it('open scope returns non-accepted deals and deals whose rejection is not active', function () {
    expect(DealRepository::get(DealScopeEnum::OPEN))->toHaveCount($this->openDealCount + $this->inactiveRejectedDealCount);
});

it('accepted scope returns accepted deals', function () {
    expect(DealRepository::get(DealScopeEnum::ACCEPTED))->toHaveCount($this->acceptedDealCount);
});

it('rejected scope returns deals with an active rejection', function () {
    expect(DealRepository::get(DealScopeEnum::DECLINED))->toHaveCount($this->activeRejectedDealCount);
});
