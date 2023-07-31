<?php

use App\Helper\DateHelper;
use Carbon\Carbon;

it('returns the correct diff in weekdays for a start and end date between monday and friday', function (Carbon $monday, Carbon $endDate, int $expectedDiffInDays) {
    expect(DateHelper::weekdayCount($monday, $endDate))->toBe($expectedDiffInDays);
})->with([
    [new Carbon('2023-07-24'), new Carbon('2023-07-24'), 1],
    [new Carbon('2023-07-24'), new Carbon('2023-07-25'), 2],
    [new Carbon('2023-07-24'), new Carbon('2023-07-26'), 3],
    [new Carbon('2023-07-24'), new Carbon('2023-07-27'), 4],
    [new Carbon('2023-07-24'), new Carbon('2023-07-28'), 5],
]);

it('returns the correct diff in weekdays for an end date in the next week', function (Carbon $friday, Carbon $endDate, int $expectedDiffInDays) {
    expect(DateHelper::weekdayCount($friday, $endDate))->toBe($expectedDiffInDays);
})->with([
    [new Carbon('2023-07-21'), new Carbon('2023-07-24'), 2],
    [new Carbon('2023-07-21'), new Carbon('2023-07-28'), 6],
]);

it('returns the correct diff in weekdays if one of the dates is on weekend', function (Carbon $startDate, Carbon $endDate, int $expectedDiffInDays) {
    expect(DateHelper::weekdayCount($startDate, $endDate))->toBe($expectedDiffInDays);
})->with([
    [new Carbon('2023-07-22'), new Carbon('2023-07-23'), 0],
    [new Carbon('2023-07-21'), new Carbon('2023-07-23'), 1],
    [new Carbon('2023-07-22'), new Carbon('2023-07-24'), 1],
]);

it('returns the correct diff in weekdays for dates spanning two weekends', function () {
    $friday = new Carbon('2023-07-21');
    $monday = new Carbon('2023-07-31');

    expect(DateHelper::weekdayCount($friday, $monday))->toBe(7);
});
