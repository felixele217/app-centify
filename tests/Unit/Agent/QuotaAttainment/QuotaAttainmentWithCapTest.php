<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly or deals that exceed the cap', function (TimeScopeEnum $timeScope, int $dealCount) {
    $plan = Plan::factory()->active()
        ->hasCap([
            'value' => $cap = 100_000_00,
        ])->create(['target_amount_per_month' => 100_000_00]);

    Deal::factory($dealCount)
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::yesterday())
        ->create([
            'value' => $cap * 2,
        ]);

    $plan->agents()->attach(Agent::first());

    expect((new QuotaAttainmentService($plan->agents->first(), $timeScope))->calculate())->toBe(floatval(1));
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 3],
    [TimeScopeEnum::ANNUALY, 12],
]);
