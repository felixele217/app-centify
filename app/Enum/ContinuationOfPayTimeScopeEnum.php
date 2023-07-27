<?php

declare(strict_types=1);

namespace App\Enum;

enum ContinuationOfPayTimeScopeEnum: string
{
    case QUARTER = 'last 13 weeks';

    public function amountOfDays(): int
    {
        return match ($this->name) {
            self::QUARTER->name => 91,
        };
    }
}
