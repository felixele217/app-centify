<?php

use App\Models\Agent;

it('calculates the commission properly', function () {
    $agent = Agent::factory()->hasDeals(2, [
        'value' => 20_000_00,
    ]);

    // expect($agent->commission)->toBe()
})->skip();
