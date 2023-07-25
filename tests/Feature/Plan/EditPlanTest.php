<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Agent;
use App\Models\Plan;
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
            ->has('target_variable_options', count(TargetVariableEnum::cases()))
            ->has('payout_frequency_options', count(PayoutFrequencyEnum::cases()))
            ->has('plan')
            ->where('plan.id', $plan->id)
            ->has('plan.cliff')
            ->has('plan.agents', $agentCount)
    );
});
