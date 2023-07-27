<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly or deals that exceed the cap', function (TimeScopeEnum $timeScope, int $dealCount) {
    $plan = Plan::factory()
        ->active()
        ->hasCap([
            'value' => $cap = 100_000_00,
        ])
        ->create([
            'target_amount_per_month' => 100_000_00,
        ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($dealCount, [
        'accepted_at' => Carbon::yesterday(),
        'value' => $cap * 2,
    ])->create());

    expect((new QuotaAttainmentService())->calculate($agent, $timeScope))->toBe(floatval(1));
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 3],
    [TimeScopeEnum::ANNUALY, 12],
]);
