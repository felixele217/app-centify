<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id, fake()->randomElement(TriggerEnum::cases()))
        ->create();
});

it('cannot store a split with a percentage_share of 0', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => Agent::factory()->ofOrganization($this->admin->organization_id)->create()->id,
                'deal_percentage' => 0,
            ],
        ],
    ])->assertInvalid([
        'partners.0.deal_percentage' => "The partners' shared percentage must be greater than 0.",
    ]);
});

it('returns correct validation error messages', function () {
    $this->put(route('deals.splits.upsert', $this->deal), [
        'partners' => [
            [
                'id' => null,
                'deal_percentage' => null,
            ],
        ],
    ])->assertInvalid([
        'partners.0.id' => "The partners' identifier field is required.",
        'partners.0.deal_percentage' => "The partners' shared percentage field is required.",
    ]);
});
