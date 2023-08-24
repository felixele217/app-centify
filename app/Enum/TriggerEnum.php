<?php

declare(strict_types=1);

namespace App\Enum;

enum TriggerEnum: string
{
    case DEMO_SCHEDULED = 'Demo scheduled';
    case DEAL_WON = 'Deal won';
}
