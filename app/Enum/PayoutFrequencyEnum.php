<?php

declare(strict_types=1);

namespace App\Enum;

enum PayoutFrequencyEnum: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
}
