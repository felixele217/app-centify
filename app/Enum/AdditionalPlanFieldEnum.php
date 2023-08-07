<?php

declare(strict_types=1);

namespace App\Enum;

enum AdditionalPlanFieldEnum: string
{
    case KICKER = 'Kicker';
    case CLIFF = 'Cliff';
    case CAP = 'Cap';
}
