<?php

use App\Enum\TriggerEnum;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

uses(TestCase::class)->group('architecture')->in('Architecture');
uses(TestCase::class)->group('integration')->in('Integration');
uses(TestCase::class)->group('feature')->in('Feature');
uses(TestCase::class)->group('unit')->in('Unit');
uses(TestCase::class)->group('staging')->in('Staging');
uses(RefreshDatabase::class)->in('Feature', 'Unit', 'Staging', 'Integration');

uses(TestCase::class)->group('local')->in('Local');

function createActivePlanWithAgent(int $organizationId, float $quotaAttainmentPerMonth, TriggerEnum $trigger, Carbon $addTime = null): array
{
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 1_000_00,
        'organization_id' => $organizationId,
        'trigger' => $trigger->value,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
        'organization_id' => $organizationId,
    ]));

    Deal::factory()
        ->withAgentDeal($agent->id, $trigger, $addTime ?? Carbon::now()->firstOfMonth())
        ->create([
            'add_time' => $addTime ?? Carbon::now()->firstOfMonth(),
            'won_time' => $trigger === TriggerEnum::DEAL_WON ? Carbon::now()->firstOfMonth() : null,
            'value' => $targetAmountPerMonth * $quotaAttainmentPerMonth,
        ]);

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

expect()->extend('toRedirectToComponent', function (string $componentName, string $env) {
    config(['app.env' => $env]);

    return test()->get($this->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component($componentName)
    );
});
