<?php

use App\Enum\TimeScopeEnum;

function queryTimeScope(): TimeScopeEnum
{
    $timeScopeQuery = request()->query('time_scope') ?? TimeScopeEnum::MONTHLY->value;

    $timeScope = TimeScopeEnum::tryFrom($timeScopeQuery);

    return $timeScope ?? TimeScopeEnum::MONTHLY;
}
