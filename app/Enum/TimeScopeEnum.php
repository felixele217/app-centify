<?php

declare(strict_types=1);

namespace App\Enum;

enum TimeScopeEnum: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case ANNUALY = 'annually';

    public function monthCount(): int
    {
        return match ($this) {
            self::MONTHLY => 1,
            self::QUARTERLY => 3,
            self::ANNUALY => 12,
        };
    }
}
