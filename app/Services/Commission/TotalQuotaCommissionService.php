<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Models\Agent;
use App\Models\Plan;

class TotalQuotaCommissionService extends TimeScopedCommissionService
{
    public function calculate(Agent $agent): ?int
    {
        return $agent->plans()->active($this->dateInScope)->get()
            ->map(fn (Plan $plan) => (new PlanQuotaCommissionService($this->timeScope, $this->dateInScope))->calculate($agent, $plan))
            ->sum();
    }
}
