<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Repositories\PaidLeaveRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PaidLeaveDaysService
{
    public function paidLeaveDays(Agent $agent, TimeScopeEnum $timeScope, AgentStatusEnum $leaveReason = null): array
    {
        $paidLeaves = PaidLeaveRepository::get($agent, $timeScope);

        if ($leaveReason) {
            $paidLeaves = $paidLeaves->filter(fn (PaidLeave $paidLeave) => $paidLeave->reason->value === $leaveReason->value);
        }

        return $this->days($paidLeaves);
    }

    private function days(Collection $paidLeaves): array
    {
        $paidLeaveDays = [];

        foreach ($paidLeaves as $sickLeave) {
            $paidLeaveDays[] = $this->weekdaysBetweenDates($sickLeave->start_date, $sickLeave->end_date);
        }

        $paidLeaveDays = array_merge(...$paidLeaveDays);

        usort($paidLeaveDays, function (Carbon $date1, Carbon $date2) {
            return $date1->timestamp - $date2->timestamp;
        });

        return $paidLeaveDays;
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
