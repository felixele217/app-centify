<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Split;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment correctly with one split deal', function () {
    $sharedPercentage = 50;

    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(1)->state([
            'accepted_at' => Carbon::now()->firstOfMonth(),
            'add_time' => Carbon::now()->firstOfMonth(),
            'value' => 1_000_00,
        ]))->create());

    $agent->deals()->first()->splits()->create([
        'agent_id' => Agent::factory()->ofOrganization($agent->organization_id)->create(),
        'shared_percentage' => $sharedPercentage,
    ]);

    expect((new QuotaAttainmentService())->calculate($agent, TimeScopeEnum::MONTHLY))->toBe(floatval(0.5));
});
