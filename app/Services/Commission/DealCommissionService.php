<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class DealCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope, float $quotaAttainment): int
    {
        if ($agent->plans()->active()->first()?->cliff?->threshold_in_percent > $quotaAttainment) {
            return 0;
        }

        $annualCommission = $quotaAttainment * ($agent->on_target_earning - $agent->base_salary);

        $dealCommission = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        };

        return intval(round($dealCommission));
    }
}
