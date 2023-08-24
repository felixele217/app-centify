<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\PlanQuotaAttainmentService;

class PlanQuotaCommissionService
{
    public function calculate(Agent $agent, Plan $plan, TimeScopeEnum $timeScope): ?int
    {
        $planQuotaAttainment = (new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate();

        return $this->calculateCommissionFromQuota($agent, $timeScope, $planQuotaAttainment);
    }

    private function calculateCommissionFromQuota(Agent $agent, TimeScopeEnum $timeScope, ?float $quotaAttainmentForTimeScope): ?int
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
