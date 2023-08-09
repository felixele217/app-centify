<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class CommissionFromQuotaService
{
    public function __construct()
    {
    }

    public function calculate(Agent $agent, TimeScopeEnum $timeScope, ?float $quotaAttainmentForTimeScope): ?int
    {
        if (is_null($quotaAttainmentForTimeScope)) {
            return null;
        }

        $annualCommission = $quotaAttainmentForTimeScope * ($agent->on_target_earning - $agent->base_salary);

        $commissionFromQuota = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommission / 12,
            TimeScopeEnum::QUARTERLY => $annualCommission / 4,
            TimeScopeEnum::ANNUALY => $annualCommission,
        };

        return intval(round($commissionFromQuota));
    }
}
