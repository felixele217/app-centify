<?php

namespace App\Enum;

enum DealScopeEnum: string
{
    case ALL = 'all';
    case OPEN = 'open';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}
