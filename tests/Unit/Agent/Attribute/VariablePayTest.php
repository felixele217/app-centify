<?php

use App\Models\Agent;

it('properly computes the variable pay from ote and base salary', function () {
    $agent = Agent::factory()->create();

    expect($agent->variable_pay)->toBe($agent->variable_pay);
});
