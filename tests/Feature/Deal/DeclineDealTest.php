<?php

use App\Models\Deal;

it('stores a rejection upon rejecting a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create();

    $this->put(route('deals.update', $deal), [
        'rejection_reason' => $reason = 'quality was poor',
    ])->assertRedirect();

    $rejection = $deal->fresh()->rejections->first();

    expect($rejection->reason)->toBe($reason);
});

it('reason is required for declining a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create();

    $this->put(route('deals.update', $deal), [
        'rejection_reason' => null,
    ])->assertInvalid([
        'rejection_reason' => 'You must provide a reason.'
    ]);
});

it('rejection_reason is not required if there is an empty note', function () {
    signInAdmin();

    $deal = Deal::factory()->create();

    $this->put(route('deals.update', $deal), [
        'note' => null,
    ])->assertValid();
})->todo();
