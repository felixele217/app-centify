<?php

use App\Models\Agent;
use App\Services\Commission\PaidLeaveCommissionService;

it('computes the paid leaves commission attribute correctly', function () {
    $agent = Agent::factory()->hasPaidLeaves(3)->create();

    expect($agent->paid_leaves_commission)->toBe((new PaidLeaveCommissionService(queryTimeScope()))->calculate($agent));
});
