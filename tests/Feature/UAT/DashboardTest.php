<?php

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\TotalCommissionService;
use App\Services\Commission\TotalQuotaCommissionChangeService;
use App\Services\QuotaAttainment\TotalQuotaAttainmentChangeService;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

it('returns the correct values for the agent', function (TimeScopeEnum $timeScope) {
    $admin = signInAdmin();

    $agent = Agent::factory()->create(['organization_id' => $admin->organization_id]);

    $sdrPlan = Plan::factory()->active()
        ->hasKicker()
        ->hasCap()
        ->create(['trigger' => TriggerEnum::DEMO_SCHEDULED->value]);

    $aePlan = Plan::factory()->active()
        ->hasKicker()
        ->hasCap()
        ->create(['trigger' => TriggerEnum::DEAL_WON->value]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, DateHelper::dateInPreviousTimeScope($timeScope))
        ->create();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, DateHelper::dateInPreviousTimeScope($timeScope))
        ->won(DateHelper::dateInPreviousTimeScope($timeScope))
        ->create();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create();

    AgentPlan::factory()->create([
        'plan_id' => $aePlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 70,
    ]);

    AgentPlan::factory()->create([
        'plan_id' => $sdrPlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 30,
    ]);

    foreach ([AgentStatusEnum::SICK, AgentStatusEnum::VACATION] as $index => $reason) {
        $agent->paidLeaves()->create([
            'reason' => $reason->value,
            'start_date' => Carbon::now()->firstOfMonth()->addWeekdays(1 + $index * 5),
            'end_date' => Carbon::now()->firstOfMonth()->addWeekdays(4 + $index * 5),
            'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::QUARTER->value,
            'sum_of_commissions' => 100000,
        ]);
    }

    $expectedQuotaAttainmentChange = (new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope) === 1.0
        ? 1
        : (new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope);

    $this->get(route('dashboard').'?time_scope='.$timeScope->value)
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->where('agents.0.commission', (new TotalCommissionService($timeScope))->calculate($agent))
                ->where('agents.0.commission_change', (new TotalQuotaCommissionChangeService())->calculate($agent, $timeScope))
                ->where('agents.0.quota_attainment_in_percent', intval((new TotalQuotaAttainmentService($agent, $timeScope))->calculate() * 100))
                ->where('agents.0.quota_attainment_change', $expectedQuotaAttainmentChange)
        );
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
]);
