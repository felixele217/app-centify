<?php

use App\Models\Agent;
use App\Models\Deal;

it('can store a split for a deal', function () {
    $admin = signInAdmin();

    $deal = Deal::factory()
        ->withAgentOfOrganization($admin->organization_id)
        ->create();

    $this->post(route('deals.splits.store', $deal), [
        'partners' => [
            [
                'shared_percentage' => $sharedPercentage = 50,
                'id' => $agentId = Agent::factory()->ofOrganization($admin->organization_id)->create()->id,
            ],
        ],
    ]);

    expect($deal->fresh()->splits()->first()->agent_id)->toBe($agentId);
    expect($deal->fresh()->splits()->first()->shared_percentage)->toBe($sharedPercentage);
});
