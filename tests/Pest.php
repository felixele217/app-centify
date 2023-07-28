<?php

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Admin;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in(
    'Feature',
    'Unit',
    'Integration',
);

function createPlanWithAgent(int $organizationId, float $quotaAttainmentPerMonth, Carbon $addTime = null): array
{
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 1_000_00,
        'organization_id' => $organizationId,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(1, [
        'add_time' => $addTime ?? Carbon::now()->firstOfMonth(),
        'accepted_at' => $addTime ?? Carbon::now()->firstOfMonth(),
        'value' => $targetAmountPerMonth * $quotaAttainmentPerMonth,
    ])->create([
        'base_salary' =>  50_000_00,
        'on_target_earning' =>  170_000_00,
        'organization_id' => $organizationId,
    ]));

    return [$plan, $agent];
}

function signInAdmin($admin = null): Admin
{
    $admin = $admin ?: Admin::factory()->create();

    test()->actingAs($admin, 'admin');

    return $admin;
}

function signInAgent($agent = null): Agent
{
    $agent = $agent ?: Agent::factory()->create();

    test()->actingAs($agent, 'agent');

    return $agent;
}
