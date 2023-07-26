<?php

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class KickerCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        if ($this->reachedQuarterlyTargetWithinTheCurrentMonth($agent, $timeScope)
        || $this->reachedQuarterlyTargetWithinQuarter($agent, $timeScope)) {
            $kickerCommission = $agent->plans()->active()->first()?->kicker?->payout_in_percent * ($agent->base_salary / 12);
        }

        return intval(round($kickerCommission ?? 0));
    }

    private function reachedQuarterlyTargetWithinTheCurrentMonth(Agent $agent, TimeScopeEnum $timeScope): bool
    {
        return $timeScope === TimeScopeEnum::MONTHLY && $agent->plans()->active()->first()?->kicker?->threshold_in_percent * 3 <= $agent->quota_attainment;
    }

    private function reachedQuarterlyTargetWithinQuarter(Agent $agent, TimeScopeEnum $timeScope): bool
    {
        return $timeScope === TimeScopeEnum::QUARTERLY && $agent->plans()->active()->first()?->kicker?->threshold_in_percent <= $agent->quota_attainment;
    }

    // private function reachedQuarterlyTargetWithinSingleMonth(Agent $agent, TimeScopeEnum $timeScope): bool
    // {

    // }
}
