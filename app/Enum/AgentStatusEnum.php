<?php

namespace App\Enum;

enum AgentStatusEnum: string
{
    case ACTIVE = 'active';
    case SICK = 'sick';
    case VACATION = 'on vacation';
}
