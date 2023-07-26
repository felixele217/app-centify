<?php

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class KickerCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope, float $quotaAttainment): int
    {
        if ($this->reachedQuarterlyTargetWithinTheCurrentMonth($agent, $timeScope, $quotaAttainment)
        || $this->reachedQuarterlyTargetWithinQuarter($agent, $timeScope, $quotaAttainment)) {
            $kickerCommission = $agent->plans()->active()->first()?->kicker?->payout_in_percent * ($agent->base_salary / 12);
        }

        return intval(round($kickerCommission ?? 0));
    }

    private function reachedQuarterlyTargetWithinTheCurrentMonth(Agent $agent, TimeScopeEnum $timeScope, float $quotaAttainment): bool
    {
        return $timeScope === TimeScopeEnum::MONTHLY && $agent->plans()->active()->first()?->kicker?->threshold_in_percent * 3 <= $quotaAttainment;
    }

    private function reachedQuarterlyTargetWithinQuarter(Agent $agent, TimeScopeEnum $timeScope, float $quotaAttainment): bool
    {
        return $timeScope === TimeScopeEnum::QUARTERLY && $agent->plans()->active()->first()?->kicker?->threshold_in_percent <= $quotaAttainment;
    }

    // private function reachedQuarterlyTargetWithinSingleMonth(Agent $agent, TimeScopeEnum $timeScope): bool
    // {

    // }
}
