<?php

use App\Enum\TriggerEnum;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

it('returns the quota attainment in percent rounded to 2 decimals places', function () {
    $plan = Plan::factory()
        ->active()
        ->hasAgents()
        ->create([
            'target_amount_per_month' => 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    Deal::factory()
        ->withAgentDeal($plan->agents->first()->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now()->firstOfMonth()->subDays(10))
        ->create([
            'value' => 3_333_33,
            'add_time' => Carbon::now()->firstOfMonth()->subDays(10),
        ]);

    Deal::factory()
        ->withAgentDeal($plan->agents->first()->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => 6_666_66,
        ]);

    expect($plan->agents->first()->quota_attainment_change_in_percent)->toBe(33.33);
});
