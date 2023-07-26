<?php

use App\Models\Plan;
use App\Models\Agent;
use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TargetVariableEnum;

use App\Enum\PayoutFrequencyEnum;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\withoutExceptionHandling;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->hasCliff()->create([
        'organization_id' => $admin->organization->id,
        'creator_id' => $admin->id,
    ]);

    Agent::factory($agentCount = 3)->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach(Agent::all());

    withoutExceptionHandling();
    $this->get(route('plans.edit', $plan))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Edit')
            ->has('agents', $agentCount, fn (AssertableInertia $page) => $page
                ->has('id')
                ->has('name')
                ->missing('email')
                ->missing('base_salary')
                ->missing('on_target_earning')
                ->etc()
            )
            ->where('target_variable_options', array_column(TargetVariableEnum::cases(), 'value'))
            ->where('payout_frequency_options', array_column(PayoutFrequencyEnum::cases(), 'value'))
            ->where('kicker_type_options', array_column(KickerTypeEnum::cases(), 'value'))
            ->where('salary_type_options', array_column(SalaryTypeEnum::cases(), 'value'))
            ->has('plan')
            ->where('plan.id', $plan->id)
            ->has('plan.cliff')
            ->has('plan.kicker')
            ->has('plan.agents', $agentCount)
    );
});
