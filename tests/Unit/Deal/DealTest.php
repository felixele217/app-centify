<?php

use App\Models\Deal;
use App\Models\Rejection;
use Carbon\Carbon;

it('latest rejection is computed correctly', function () {
    $deal = Deal::factory()->has(
        Rejection::factory()->count(2)->sequence(
            [
                'created_at' => Carbon::yesterday(),
                'reason' => $earlierRejectionReason = 'some reason',
            ],
            [
                'created_at' => Carbon::today(),
                'reason' => $latestRejectionReason = 'some other reason',
            ],
        )
    )->create();

    expect($deal->latestRejection->reason)->toBe($latestRejectionReason);
});
