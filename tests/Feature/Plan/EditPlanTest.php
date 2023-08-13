<?php

use App\Enum\KickerTypeEnum;
use App\Enum\PlanCycleEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()
        ->hasAgents($agentCount = 3, [
            'organization_id' => $admin->organization->id,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    $this->get(route('plans.edit', $plan))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Edit')
            ->has('agents', $agentCount)
            ->where('target_variable_options', array_column(TargetVariableEnum::cases(), 'value'))
            ->where('payout_frequency_options', array_column(PlanCycleEnum::cases(), 'value'))
            ->where('kicker_type_options', array_column(KickerTypeEnum::cases(), 'value'))
            ->where('salary_type_options', array_column(SalaryTypeEnum::cases(), 'value'))
            ->has('plan')
            ->where('plan.id', $plan->id)
            ->has('plan.cliff')
            ->has('plan.kicker')
            ->has('plan.cap')
            ->has('plan.agents', $agentCount)
    );
});
