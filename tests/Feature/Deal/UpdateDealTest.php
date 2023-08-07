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

it('can update the note of a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'note' => null,
    ]);

    $this->put(route('deals.update', $deal), [
        'note' => $newNote = 'simeon thought out some sophisticated note that is very long',
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBe($newNote);
});
