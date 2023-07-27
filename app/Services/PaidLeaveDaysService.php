<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Repositories\PaidLeaveRepository;
use Carbon\Carbon;

class PaidLeaveDaysService
{
    public function sickDays(Agent $agent, TimeScopeEnum $timeScope): array
    {
        $sickLeaves = PaidLeaveRepository::get($agent, $timeScope)->filter(fn (PaidLeave $paidLeave) => $paidLeave->reason->value === AgentStatusEnum::SICK->value);

        $sickLeaveDays = [];

        foreach ($sickLeaves as $sickLeave) {
            $sickLeaveDays[] = $this->weekdaysBetweenDates($sickLeave->start_date, $sickLeave->end_date);
        }

        $sickLeaveDays = array_merge(...$sickLeaveDays);

        usort($sickLeaveDays, function ($a, $b) {
            return $a->timestamp - $b->timestamp;
        });

        return $sickLeaveDays;
    }

    private function weekdaysBetweenDates(Carbon $start, Carbon $end): array
    {
        $dates = [];

        for ($date = $start; $date->lte($end); $date->addWeekdays(1)) {
            $dates[] = $date->copy();
        }

        return $dates;
    }
}
