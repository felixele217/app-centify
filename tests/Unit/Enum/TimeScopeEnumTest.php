<?php

use App\Enum\TimeScopeEnum;

it('returns the amount of months for a given timescope', function (TimeScopeEnum $timeScope, int $expectedMonthCount) {
    expect($timeScope->monthCount())->toBe($expectedMonthCount);
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 3],
    [TimeScopeEnum::ANNUALY, 12],
]);
