<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly or deals that exceed the cap', function (TimeScopeEnum $timeScope, int $dealCount) {
    $plan = Plan::factory()->active()
        ->hasCap([
            'value' => $cap = 100_000_00,
        ])->create(['target_amount_per_month' => 100_000_00]);

    $agent = Agent::factory()->create();

    Deal::factory($dealCount)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->won()
        ->create([
            'value' => $cap * 2,
        ]);

    $plan->agents()->attach($agent);

    expect((new TotalQuotaAttainmentService($plan->agents->first(), $timeScope))->calculate())->toBe(floatval(1));
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 3],
    [TimeScopeEnum::ANNUALY, 12],
]);
