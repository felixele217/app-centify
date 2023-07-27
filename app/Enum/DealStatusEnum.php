<?php

declare(strict_types=1);

namespace App\Enum;

enum DealStatusEnum: string
{
    case OPEN = 'open';
    case WON = 'won';
    case LOST = 'lost';
}
