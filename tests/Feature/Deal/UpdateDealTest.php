<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;

it('can accept a deal that has a demo_scheduled trigger', function () {
    signInAdmin();

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->create();

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->sdr->pivot->accepted_at)->not()->toBeNull();
    expect($deal->rejections->count())->toBe(0);
});

it('can accept a deal that has a deal_won trigger', function () {
    signInAdmin();

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEAL_WON, null)
        ->create([
            'won_time' => Carbon::yesterday(),
        ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->ae->pivot->accepted_at)->not()->toBeNull();
    expect($deal->rejections->count())->toBe(0);
});

it('can update the note of a deal', function () {
    signInAdmin();

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->create([
            'note' => null,
        ]);

    $this->put(route('deals.update', $deal), [
        'note' => $newNote = 'simeon thought out some sophisticated note that is very long',
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBe($newNote);
});

it('can update the note of a deal setting it to null', function () {
    signInAdmin();

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->create([
            'note' => 'some existing note',
        ]);

    $this->put(route('deals.update', $deal), [
        'note' => null,
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBeNull();
});

it('does not remove the note upon accepting a deal', function () {
    signInAdmin();

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->create([
            'note' => $note = 'some note',
        ]);

    $this->put(route('deals.update', $deal), [
        'has_accepted_deal' => true,
    ])->assertRedirect();

    expect($deal->fresh()->note)->toBe($note);
});
