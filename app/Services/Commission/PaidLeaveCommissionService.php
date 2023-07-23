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
        $baseQuery = $agent->paidLeaves();

        $advancedQuery = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $baseQuery->whereMonth('end_date', Carbon::now()->month),
            TimeScopeEnum::QUARTERLY => $baseQuery->whereBetween('end_date', [Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()]),
            TimeScopeEnum::ANNUALY => $baseQuery->whereBetween('end_date', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()]),
        };

        return intval(round($advancedQuery->get()
            ->map(fn (PaidLeave $paidLeave) => $this->paidLeaveCommission($paidLeave))
            ->sum()));
    }

    private function paidLeaveCommission(PaidLeave $paidLeave): float
    {
        $commissionPerDay = $paidLeave->sum_of_commissions / $this->daysForTimeScope($paidLeave->continuation_of_pay_time_scope);

        return $commissionPerDay * $this->paidLeaveDays($paidLeave);
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
