<?php

namespace App\Enum;

enum DealScopeEnum: string
{
    case OPEN = 'open';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}
