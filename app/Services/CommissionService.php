<?php

namespace App\Services;

use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use Carbon\Carbon;

class CommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $annualCommission = $agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary);

        $baseCommission = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        };

        return intval(round($baseCommission + $this->paidLeaveCommission($agent, $timeScope)));
    }

    private function paidLeaveCommission(Agent $agent, TimeScopeEnum $timeScope): float
    {
        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => $agent->paidLeaves()
                ->whereMonth('start_date', Carbon::now()->month)
                ->whereMonth('end_date', Carbon::now()->month)
                ->get()
                ->map(function ($paidLeave) {
                    $paidLeaveDays = $paidLeave->end_date->diffInDays($paidLeave->start_date) + 1;
                    $commissionPerDay = $paidLeave->sum_of_commissions / $this->daysForTimeScope($paidLeave->continuation_of_pay_time_scope);

                    return $commissionPerDay * $paidLeaveDays;
                })->sum(),
            default => 0
        };
    }

    private function daysForTimeScope(ContinuationOfPayTimeScopeEnum $timeScope): int
    {
        return match ($timeScope) {
            ContinuationOfPayTimeScopeEnum::QUARTER => 90,
        };
    }
}
