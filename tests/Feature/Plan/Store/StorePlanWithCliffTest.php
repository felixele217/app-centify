<?php

use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;

it('can store a plan with a cliff as an admin', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cliff_threshold' => $cliffPercentage = 10,
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect(Plan::first()->cliff->threshold_in_percent)->toBe($cliffPercentage);
});
