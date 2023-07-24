<?php

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\CarbonImmutable;

class PaidLeaveCommissionService
{
    private $dateInScope;

    public function __construct()
    {
        $this->dateInScope = CarbonImmutable::now();
    }

    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $advancedQuery = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $agent->paidLeaves()
                ->whereMonth('end_date', $this->dateInScope->month)
                ->orWhereMonth('start_date', $this->dateInScope->month),
            TimeScopeEnum::QUARTERLY => $agent->paidLeaves()
                ->whereBetween('end_date', [$this->dateInScope->firstOfQuarter(), $this->dateInScope->lastOfQuarter()])
                ->orWhereBetween('start_date', [$this->dateInScope->firstOfQuarter(), $this->dateInScope->lastOfQuarter()]),
            TimeScopeEnum::ANNUALY => $agent->paidLeaves()
                ->whereBetween('end_date', [$this->dateInScope->firstOfYear(), $this->dateInScope->lastOfYear()])
                ->orWhereBetween('start_date', [$this->dateInScope->firstOfYear(), $this->dateInScope->lastOfYear()]),
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
            TimeScopeEnum::MONTHLY => $this->paidLeaveWeekdays($paidLeave, 'month', $this->dateInScope->firstOfMonth(), $this->dateInScope->lastOfMonth()),
            TimeScopeEnum::QUARTERLY => $this->paidLeaveWeekdays($paidLeave, 'quarter', $this->dateInScope->firstOfQuarter(), $this->dateInScope->lastOfQuarter()),
            TimeScopeEnum::ANNUALY => $this->paidLeaveWeekdays($paidLeave, 'year', $this->dateInScope->firstOfYear(), $this->dateInScope->lastOfYear()),
        };
    }

    private function paidLeaveWeekdays(PaidLeave $paidLeave, string $timeScope, CarbonImmutable $timeScopeStartDate, CarbonImmutable $timeScopeEndDate): int
    {
        $currentScope = data_get($this->dateInScope, $timeScope);

        if (data_get($paidLeave->start_date, $timeScope) === $currentScope && data_get($paidLeave->end_date, $timeScope) === $currentScope) {
            return $paidLeave->end_date->diffInWeekdays($paidLeave->start_date) + 1;
        } elseif (data_get($paidLeave->end_date, $timeScope) === $currentScope) {
            return $paidLeave->end_date->diffInWeekdays($timeScopeStartDate) + 1;
        } elseif (data_get($paidLeave->start_date, $timeScope) === $currentScope) {
            return $paidLeave->start_date->diffInWeekdays($timeScopeEndDate) + 1;
        }

        return 0;
    }
}
