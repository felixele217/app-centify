<?php

namespace App\Enum;

enum DealStatusEnum: string
{
    case OPEN = 'open';
    case WON = 'won';
    case LOST = 'lost';
}
