<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly or deals that exceed the cap', function () {
    $plan = Plan::factory()
        ->active()
        ->hasCap([
            'value' => $cap = 100_000_00,
        ])
        ->create([
            'target_amount_per_month' => 100_000_00
        ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::yesterday(),
        'value' => $cap * 2,
    ])->create());

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval(2));
});
