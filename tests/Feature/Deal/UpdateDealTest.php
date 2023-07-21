<?php

use App\Models\Deal;

it('can accept a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'accepted_at' => null,
    ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->accepted_at)->not()->toBeNull();
    expect($deal->fresh()->declined_at)->toBeNull();
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
