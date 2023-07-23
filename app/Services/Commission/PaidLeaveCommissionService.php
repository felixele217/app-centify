<?php

namespace App\Services\Commission;

use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

class PaidLeaveCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        return intval(round(match ($timeScope) {
            TimeScopeEnum::MONTHLY => $agent->paidLeaves()
                ->whereMonth('end_date', Carbon::now()->month)
                ->get()
                ->map(function ($paidLeave) {
                    $commissionPerDay = $paidLeave->sum_of_commissions / $this->daysForTimeScope($paidLeave->continuation_of_pay_time_scope);

                    return $commissionPerDay * $this->paidLeaveDays($paidLeave);
                })->sum(),
            default => 0
        }));
    }

    private function daysForTimeScope(ContinuationOfPayTimeScopeEnum $timeScope): int
    {
        return match ($timeScope) {
            ContinuationOfPayTimeScopeEnum::QUARTER => 90,
        };
    }

    private function paidLeaveDays(PaidLeave $paidLeave): int
    {
        return $paidLeave->end_date->diffInWeekdays($paidLeave->start_date) + 1;
    }
}
