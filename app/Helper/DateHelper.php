<?php

namespace App\Helper;

use Carbon\Carbon;

class DateHelper
{
    public static function parsePipedriveTime(string $time): Carbon
    {
        $sanitized = preg_replace('/ =>/', ':', $time);

        return Carbon::createFromFormat('Y-m-d H:i:s', $sanitized);
    }
}
