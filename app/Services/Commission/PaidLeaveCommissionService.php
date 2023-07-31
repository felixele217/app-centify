<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Repositories\PaidLeaveRepository;
use Carbon\CarbonImmutable;

class PaidLeaveCommissionService
{
    private CarbonImmutable $dateInScope;

    public function __construct()
    {
        $this->dateInScope = CarbonImmutable::now();
    }

    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $paidLeaves = PaidLeaveRepository::get($agent, $timeScope);

        return intval(round($paidLeaves
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
            return DateHelper::weekdayCount($paidLeave->start_date, $paidLeave->end_date);
        } elseif (data_get($paidLeave->end_date, $timeScope) === $currentScope) {
            return DateHelper::weekdayCount($timeScopeStartDate, $paidLeave->end_date);
        } elseif (data_get($paidLeave->start_date, $timeScope) === $currentScope) {
            return DateHelper::weekdayCount($paidLeave->start_date, $timeScopeEndDate);
        }

        return 0;
    }
}
