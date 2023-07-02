<?php

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    signIn();
  
    $this->get(route('plans.create'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Create')
           ->has('target_variable_options', count(TargetVariableEnum::cases()))
           ->has('payout_frequency_options', count(PayoutFrequencyEnum::cases()))
    );
});
