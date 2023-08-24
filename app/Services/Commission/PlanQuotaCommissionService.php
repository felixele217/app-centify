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

        return (new CommissionFromQuotaService())->calculate($agent, $timeScope, $planQuotaAttainment);
    }
}
