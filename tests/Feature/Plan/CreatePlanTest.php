<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signIn();

    User::factory($agentCount = 5)->agent()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->get(route('plans.create'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Create')
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
    );
});
