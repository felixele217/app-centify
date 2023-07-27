<?php

declare(strict_types=1);

namespace App\Enum;

enum TimeScopeEnum: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case ANNUALY = 'annually';
}
