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

it('can decline a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'declined_at' => null,
    ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => false,
    ])->assertRedirect();

    expect($deal->fresh()->declined_at)->not()->toBeNull();
    expect($deal->fresh()->accepted_at)->toBeNull();
});
