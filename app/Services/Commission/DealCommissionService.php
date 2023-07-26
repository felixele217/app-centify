<?php

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class DealCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        if ($agent->plans()->active()->first()?->cliff?->threshold_in_percent > $agent->quota_attainment) {
            return 0;
        }

        if ($agent->plans()->active()->first()?->kicker?->threshold_in_percent <= $agent->quota_attainment) {
            $kickerCommission = $agent->plans()->active()->first()?->kicker?->payout_in_percent * ($agent->base_salary / 12);
        }

        $annualCommission = $agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary);

        $dealCommission = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        };

        return intval(round($dealCommission + $kickerCommission));
    }
}
