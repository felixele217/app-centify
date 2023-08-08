<?php

use App\Models\Deal;
use App\Models\Rejection;
use Carbon\Carbon;

it('active rejection is computed correctly', function () {
    $deal = Deal::factory()->has(
        Rejection::factory()->count(2)->sequence(
            [
                'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
                'reason' => 'some reason',
            ],
            [
                'created_at' => Carbon::today(),
                'reason' => 'some other reason',
            ],
        )
    )->create();

    expect($deal->activeRejection->reason)->toBe($deal->rejections()->active()->first()->reason);
});
