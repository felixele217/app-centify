<?php

use App\Helper\DateHelper;
use App\Enum\TimeScopeEnum;
use Carbon\CarbonImmutable;

it('returns dates that are the smallest even respecting hours minutes and seconds', function (TimeScopeEnum $timeScope) {
    [$firstDateInScope, $lastDateInScope] = DateHelper::firstAndLastDateInScope(CarbonImmutable::now(), $timeScope);

      $hour = $firstDateInScope->format('H');
      $minute = $firstDateInScope->format('i');
      $second = $firstDateInScope->format('s');

      expect($hour)->toBe('00');
      expect($minute)->toBe('00');
      expect($second)->toBe('00');

      $hour = $lastDateInScope->format('H');
      $minute = $lastDateInScope->format('i');
      $second = $lastDateInScope->format('s');

      expect($hour)->toBe('23');
      expect($minute)->toBe('59');
      expect($second)->toBe('59');
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);
