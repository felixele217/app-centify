<?php

use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;

it('can store a plan with a cap as an admin', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cap' => $cap = 100_000_00,
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect(Plan::first()->cap->value)->toBe($cap);
});
