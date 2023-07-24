<?php

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

class PaidLeaveCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $advancedQuery = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $agent->paidLeaves()
                ->whereMonth('end_date', Carbon::now()->month)
                ->orWhereMonth('start_date', Carbon::now()->month),
            TimeScopeEnum::QUARTERLY => $agent->paidLeaves()
                ->whereBetween('end_date', [Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()])
                ->orWhereBetween('start_date', [Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()]),
            TimeScopeEnum::ANNUALY => $agent->paidLeaves()
                ->whereBetween('end_date', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()])
                ->orWhereBetween('start_date', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()]),
        };

        return intval(round($advancedQuery->get()
            ->map(fn (PaidLeave $paidLeave) => $this->paidLeaveCommission($paidLeave, $timeScope))
            ->sum()));
    }

    private function paidLeaveCommission(PaidLeave $paidLeave, TimeScopeEnum $timeScope): float
    {
        $commissionPerDay = $paidLeave->sum_of_commissions / $paidLeave->continuation_of_pay_time_scope->amountOfDays();

        return $commissionPerDay * $this->paidLeaveWeekdaysForTimeScope($paidLeave, $timeScope);
    }

    private function paidLeaveWeekdaysForTimeScope(PaidLeave $paidLeave, TimeScopeEnum $timeScope): int
    {
        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => $this->paidLeaveWeekdaysForMonth($paidLeave),
            TimeScopeEnum::QUARTERLY => $this->paidLeaveWeekdaysForQuarter($paidLeave),
            TimeScopeEnum::ANNUALY => $this->paidLeaveWeekdaysForYear($paidLeave),
        };
    }

    private function paidLeaveWeekdaysForMonth(PaidLeave $paidLeave): int
    {
        $currentMonth = Carbon::now()->month;

        if ($paidLeave->start_date->month === $currentMonth && $paidLeave->end_date->month === $currentMonth) {
            return $paidLeave->end_date->diffInWeekdays($paidLeave->start_date) + 1;
        } elseif ($paidLeave->end_date->month === $currentMonth) {
            return $paidLeave->end_date->diffInWeekdays(Carbon::now()->firstOfMonth()) + 1;
        } elseif ($paidLeave->start_date->month === $currentMonth) {
            return $paidLeave->start_date->diffInWeekdays(Carbon::now()->lastOfMonth()) + 1;
        }

        return 0;
    }

    private function paidLeaveWeekdaysForQuarter(PaidLeave $paidLeave): int
    {
        $currentQuarter = Carbon::now()->quarter;

        if ($paidLeave->start_date->quarter === $currentQuarter && $paidLeave->end_date->quarter === $currentQuarter) {
            return $paidLeave->end_date->diffInWeekdays($paidLeave->start_date) + 1;
        } elseif ($paidLeave->end_date->quarter === $currentQuarter) {
            return $paidLeave->end_date->diffInWeekdays(Carbon::now()->firstOfQuarter()) + 1;
        } elseif ($paidLeave->start_date->quarter === $currentQuarter) {
            return $paidLeave->start_date->diffInWeekdays(Carbon::now()->lastOfQuarter()) + 1;
        }

        return 0;
    }

    private function paidLeaveWeekdaysForYear(PaidLeave $paidLeave): int
    {
        $currentYear = Carbon::now()->year;

        if ($paidLeave->start_date->year === $currentYear && $paidLeave->end_date->year === $currentYear) {
            return $paidLeave->end_date->diffInWeekdays($paidLeave->start_date) + 1;
        } elseif ($paidLeave->end_date->year === $currentYear) {
            return $paidLeave->end_date->diffInWeekdays(Carbon::now()->firstOfYear()) + 1;
        } elseif ($paidLeave->start_date->year === $currentYear) {
            return $paidLeave->start_date->diffInWeekdays(Carbon::now()->lastOfYear()) + 1;
        }

        return 0;
    }
}
