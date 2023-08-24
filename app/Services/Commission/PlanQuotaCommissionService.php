<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Plan;
use App\Services\PlanQuotaAttainmentService;
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

        return $this->calculateCommissionFromQuota($agent, $plan, $planQuotaAttainment);
    }

    private function calculateCommissionFromQuota(Agent $agent, Plan $plan, ?float $quotaAttainmentForTimeScope): ?int
    {
        if (is_null($quotaAttainmentForTimeScope)) {
            return null;
        }

        $shareOfVariablePay = AgentPlan::whereAgentId($agent->id)->wherePlanId($plan->id)->first()->share_of_variable_pay;
        $annualShareOfVariablePay = ($quotaAttainmentForTimeScope * ($agent->on_target_earning - $agent->base_salary)) * $shareOfVariablePay;

        $commissionFromQuota = match ($this->timeScope) {
            TimeScopeEnum::MONTHLY => $annualShareOfVariablePay / 12,
            TimeScopeEnum::QUARTERLY => $annualShareOfVariablePay / 4,
            TimeScopeEnum::ANNUALY => $annualShareOfVariablePay,
        };

        return intval(round($commissionFromQuota));
    }
}
