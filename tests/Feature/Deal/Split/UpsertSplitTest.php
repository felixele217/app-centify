<?php

use App\Models\Agent;
use App\Models\Deal;
use App\Models\Split;

it('can store a split for a deal', function () {
    $admin = signInAdmin();

    $deal = Deal::factory()
        ->withAgentOfOrganization($admin->organization_id)
        ->create();

    $this->post(route('deals.splits.store', $deal), [
        'partners' => [
            [
                'id' => $agentId = Agent::factory()->ofOrganization($admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage = 50,
            ],
            [
                'id' => $agentId2 = Agent::factory()->ofOrganization($admin->organization_id)->create()->id,
                'shared_percentage' => $sharedPercentage2 = 20,
            ],
        ],
    ]);

    expect($deal->fresh()->splits()->whereAgentId($agentId)->first()->shared_percentage)->toBe($sharedPercentage / 100);
    expect($deal->fresh()->splits()->whereAgentId($agentId2)->first()->shared_percentage)->toBe($sharedPercentage2 / 100);
});

it('can updates the splits correctly if there already were some', function () {
    $admin = signInAdmin();

    $deal = Deal::factory()
        ->withAgentOfOrganization($admin->organization_id)
        ->create();

    $split = Split::factory()->create([
        'agent_id' => $existingAgentId = Agent::factory()->ofOrganization($admin->organization_id)->create()->id,
        'shared_percentage' => $existingSharedPercentage = 10,
        'deal_id' => $deal->id,
    ]);

    $this->post(route('deals.splits.store', $deal), [
        'partners' => [
            [
                'shared_percentage' => $newSharedPercentage = 50,
                'id' => $existingAgentId,
            ],
            [
                'shared_percentage' => 20,
                'id' => Agent::factory()->ofOrganization($admin->organization_id)->create()->id,
            ],
        ],
    ]);

    expect($deal->fresh()->splits()->count())->toBe(2);
    expect($split->fresh()->shared_percentage)->toBe($newSharedPercentage / 100);
});

it('removes the split if it is not present in the request', function () {
    $admin = signInAdmin();

    $deal = Deal::factory()
        ->withAgentOfOrganization($admin->organization_id)
        ->create();

    Split::factory()->create(['deal_id' => $deal->id]);

    $this->post(route('deals.splits.store', $deal), [
        'partners' => [],
    ]);

    expect($deal->fresh()->splits()->count())->toBe(0);
});
