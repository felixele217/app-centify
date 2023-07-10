<?php

use App\Models\Deal;

it('can update a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'accepted_at' => null,
    ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->accepted_at)->not()->toBeNull();
});
