<?php

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class CommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $annualCommission = $agent->quota_attainment * ($agent->on_target_earning - $agent->base_salary);

        $calculatedCommission = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        };

        return intval(round($calculatedCommission));
    }
}
