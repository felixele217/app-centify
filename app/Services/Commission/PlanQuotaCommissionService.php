<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;
use Carbon\CarbonImmutable;

class PlanQuotaCommissionService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null,
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(Agent $agent, Plan $plan): ?int
    {
        $planQuotaAttainment = (new PlanQuotaAttainmentService($agent, $plan, $this->timeScope, $this->dateInScope))->calculate();

        $shareOfVariablePay = $plan->agents()->whereAgentId($agent->id)->first()->pivot->share_of_variable_pay;

        return intval(round($shareOfVariablePay * $this->calculateCommissionFromQuota($agent, $planQuotaAttainment)));
    }

    private function calculateCommissionFromQuota(Agent $agent, ?float $quotaAttainmentForTimeScope): ?float
    {
        if (is_null($quotaAttainmentForTimeScope)) {
            return null;
        }

        $annualCommissionFromQuota = $quotaAttainmentForTimeScope * ($agent->variable_pay);

        $commissionFromQuota = match ($this->timeScope) {
            TimeScopeEnum::MONTHLY => $annualCommissionFromQuota / 12,
            TimeScopeEnum::QUARTERLY => $annualCommissionFromQuota / 4,
            TimeScopeEnum::ANNUALY => $annualCommissionFromQuota,
        };

        return $commissionFromQuota;
    }
}
