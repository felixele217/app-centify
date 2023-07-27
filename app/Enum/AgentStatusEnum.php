<?php

declare(strict_types=1);

namespace App\Enum;

enum AgentStatusEnum: string
{
    case ACTIVE = 'active';
    case SICK = 'sick';
    case VACATION = 'on vacation';
}
