<?php

declare(strict_types=1);

namespace App\Enum;

enum ContinuationOfPayTimeScopeEnum: string
{
    case QUARTER = 'last 13 weeks';
    case SEMI_YEAR = 'last 6 months';
    case YEAR = 'last year';

    public function amountOfDays(): int
    {
        return match ($this->name) {
            self::QUARTER->name => 91,
            self::SEMI_YEAR->name => 180,
            self::YEAR->name => 365,
        };
    }
}
