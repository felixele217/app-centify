<?php

declare(strict_types=1);

namespace App\Enum;

enum PlanCycleEnum: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
}
