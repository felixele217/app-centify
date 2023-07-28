<?php

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;

it('returns immutable date in previous specified timescope', function () {
    $timeScope = TimeScopeEnum::MONTHLY;

    expect(DateHelper::dateInPreviousTimeScope($timeScope)->isLastQuarter())->toBeTrue();
});
