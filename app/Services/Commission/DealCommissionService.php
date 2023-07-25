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

        $annualCommission = $agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary);

        return intval(round(match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        }));
    }
}
