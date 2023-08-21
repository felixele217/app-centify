<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enum\TimeScopeEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class DateHelper
{
    public static function parsePipedriveTime(string $time): Carbon
    {
        $sanitized = preg_replace('/ =>/', ':', $time);

        return Carbon::createFromFormat('Y-m-d H:i:s', $sanitized);
    }

    public static function weekdayCount(Carbon|CarbonImmutable $startDate, Carbon|CarbonImmutable $endDate): int
    {
        if ($endDate->isWeekend()) {
            return $startDate->diffInWeekdays($endDate);
        }

        return $startDate->diffInWeekdays($endDate) + 1;
    }

    public static function dateInPreviousTimeScope(TimeScopeEnum $timeScope): CarbonImmutable
    {
        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => CarbonImmutable::now()->firstOfMonth()->subDays(10),
            TimeScopeEnum::QUARTERLY => CarbonImmutable::now()->firstOfQuarter()->subDays(10),
            TimeScopeEnum::ANNUALY => CarbonImmutable::now()->firstOfYear()->subDays(10),
        };
    }

    public static function firstAndLastDateInScope(CarbonImmutable $dateInScope, TimeScopeEnum $timeScope): array
    {
        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => [$dateInScope->firstOfMonth(), $dateInScope->lastOfMonth()],
            TimeScopeEnum::QUARTERLY => [$dateInScope->firstOfQuarter(), $dateInScope->lastOfQuarter()],
            TimeScopeEnum::ANNUALY => [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()],
        };
    }
}
