<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use Carbon\CarbonImmutable;

class TotalQuotaAttainmentService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private Agent $agent,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(): ?float
    {
        $activePlans = $this->agent->load('plans')->plans()->active($this->dateInScope)->get();

        if (! $activePlans->count()) {
            return null;
        }

        $totalQuotaAttainment = 0;

        foreach ($activePlans as $activePlan) {
            $totalQuotaAttainment += (new QuotaAttainmentPerTriggerService($this->agent, $activePlan->trigger, $this->timeScope, $this->dateInScope))->calculate();
        }

        return $totalQuotaAttainment;
    }
}
