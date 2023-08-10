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
    expect($deal->rejections->count())->toBe(0);
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

it('can update the note of a deal setting it to null', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'note' => 'some existing note',
    ]);

    $this->put(route('deals.update', $deal), [
        'note' => null,
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBeNull();
});

it('does not remove the note upon accepting a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create([
        'accepted_at' => null,
        'note' => $note = 'some note',
    ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBe($note);
});
