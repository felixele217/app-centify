<?php

use App\Models\Deal;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('returns only the deals that are accepted', function () {
    Deal::factory($dealCount = 2)->create(['accepted_at' => Carbon::now()->firstOfMonth()]);

    expect(Deal::accepted()->count())->toBe($dealCount);
});

it('returns only the deals that are accepted before the passed date', function () {
    $cutoffDate = CarbonImmutable::parse('-9 days');

    Deal::factory($acceptedBeforeCutoffCount = 2)->create(['accepted_at' => Carbon::parse('-10 days')]);
    Deal::factory($acceptedAfterCutoffCount = 3)->create(['accepted_at' => Carbon::parse('-7 days')]);

    expect(Deal::accepted($cutoffDate)->count())->toBe($acceptedBeforeCutoffCount);
});

it('does not return the deals that are not yet accepted', function () {
    Deal::factory()->create(['accepted_at' => null]);

    expect(Deal::accepted()->count())->toBe(0);
});
