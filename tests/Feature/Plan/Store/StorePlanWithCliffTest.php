<?php

use App\Models\Plan;
use App\Models\Cliff;
use App\Enum\TimeScopeEnum;
use App\Http\Requests\StorePlanRequest;

it('can store a plan with a cliff as an admin', function (TimeScopeEnum $timeScope) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => $cliffPercentage = 10,
            'time_scope' => $timeScope->value,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect(Plan::first()->cliff->threshold_in_percent)->toBe($cliffPercentage / 100);
    expect(Plan::first()->cliff->time_scope->value)->toBe($timeScope->value);
})->with(TimeScopeEnum::cases());

it('requires all cliff fields if at least one is specified', function (array $providedField, array $missingFields) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cliff' => $providedField,
    ])->fake();

    $this->post(route('plans.store'))->assertInvalid($missingFields);
})->with([
    [
        [
            'threshold_in_percent' => 25,
        ],
        [
            'cliff.time_scope' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ],
    ],
    [
        [
            'time_scope' => TimeScopeEnum::MONTHLY->value,
        ],
        [
            'cliff.threshold_in_percent' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ],
    ],
]);

it('does not throw validation errors if you send 0 as values in either of the percent fields', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => 0,
            'time_scope' => null,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertValid();
});

it('does not store a cliff when an array with empty values is sent', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => 0,
            'time_scope' => null,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect();

    expect(Cliff::count())->toBe(0);
});
