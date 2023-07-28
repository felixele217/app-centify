<?php

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;

it('returns immutable date in previous specified timescope for monthly', function () {
    expect(DateHelper::dateInPreviousTimeScope(TimeScopeEnum::MONTHLY)->isLastMonth())->toBeTrue();
});

it('returns immutable date in previous specified timescope for quarterly', function () {
    expect(DateHelper::dateInPreviousTimeScope(TimeScopeEnum::QUARTERLY)->isLastQuarter())->toBeTrue();
});

it('returns immutable date in previous specified timescope for annualy', function () {
    expect(DateHelper::dateInPreviousTimeScope(TimeScopeEnum::ANNUALY)->isLastQuarter())->toBeTrue();
});
