<?php

use App\Enum\KickerTypeEnum;
use App\Enum\PlanCycleEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TargetVariableEnum;
use App\Models\Agent;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    Agent::factory($agentCount = 5)->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->get(route('plans.create'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Create')
            ->has('agents', $agentCount)
            ->where('target_variable_options', array_column(TargetVariableEnum::cases(), 'value'))
            ->where('payout_frequency_options', array_column(PlanCycleEnum::cases(), 'value'))
            ->where('kicker_type_options', array_column(KickerTypeEnum::cases(), 'value'))
            ->where('salary_type_options', array_column(SalaryTypeEnum::cases(), 'value'))
    );
});
