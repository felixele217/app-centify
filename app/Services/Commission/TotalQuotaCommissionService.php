<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use Carbon\CarbonImmutable;

class TotalQuotaCommissionService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null,
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(Agent $agent): ?int
    {
        return $agent->plans()->active($this->dateInScope)->get()
            ->map(fn (Plan $plan) => (new PlanQuotaCommissionService($this->timeScope, $this->dateInScope))->calculate($agent, $plan))
            ->sum();
    }
}
