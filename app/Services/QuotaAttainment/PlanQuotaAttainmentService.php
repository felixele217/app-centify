<?php

declare(strict_types=1);

namespace App\Services\QuotaAttainment;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class PlanQuotaAttainmentService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private Agent $agent,
        private Plan $plan,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(): ?float
    {
        $deals = DealRepository::dealsForAgent($this->agent, $this->timeScope, $this->dateInScope);

        $deals = $deals->filter(function (Deal $deal) {
            return $deal->agents()->whereAgentId($this->agent->id)->wherePivot('triggered_by', $this->plan->trigger)->exists();
        });

        return $this->cappedSumOfDeals($deals) / ($this->plan->target_amount_per_month * $this->timeScope->monthCount());
    }

    private function cappedSumOfDeals(Collection $deals): float
    {
        $cap = $this->plan->load('cap')->cap?->value;

        return array_sum($deals->map(fn (Deal $deal) => $this->cappedValue($deal, $cap))->toArray());
    }

    private function cappedValue(Deal $deal, ?int $cap): float
    {
        $dealValue = $deal->value * $deal->percentageFactorForAgent($this->agent);

        if ((bool) $cap) {
            return min($dealValue, $cap);
        }

        return $dealValue;
    }
}
