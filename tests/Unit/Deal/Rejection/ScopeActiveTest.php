<?php

use App\Models\Rejection;
use Carbon\Carbon;

it('active rejection returns the rejection of this month', function () {
    $deprecatedRejection = Rejection::factory()->create([
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
    ]);

    $rejection = Rejection::factory()->create([
        'created_at' => Carbon::now()->firstOfMonth(),
    ]);

    expect(Rejection::active()->first()->reason)->toBe($rejection->reason);
});

it('active rejection returns the permanent rejection', function () {
    $deprecatedRejection = Rejection::factory()->create([
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
    ]);

    $rejection = Rejection::factory()->create([
        'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        'is_permanent' => true,
    ]);

    expect(Rejection::active()->first()->reason)->toBe($rejection->reason);
});
