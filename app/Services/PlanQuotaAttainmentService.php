<?php

declare(strict_types=1);

namespace App\Services;

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
        private Plan $activePlan,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(): ?float
    {
        $deals = DealRepository::dealsForAgent($this->agent, $this->timeScope, $this->dateInScope);

        $deals = $deals->filter(function (Deal $deal) {
            return $deal->agents()->whereAgentId($this->agent->id)->wherePivot('triggered_by', $this->activePlan->trigger)->exists();
        });

        return $this->cappedSumOfDeals($deals) / ($this->activePlan->target_amount_per_month * $this->timeScope->monthCount());
    }

    private function cappedSumOfDeals(Collection $deals): float
    {
        return array_sum($deals->map(fn (Deal $deal) => $this->cappedValue($deal, $this->activePlan->load('cap')->cap?->value))->toArray());
    }

    private function cappedValue(Deal $deal, ?int $cap): float
    {
        $dealValue = $deal->value * $deal->agents()->whereAgentId($this->agent->id)->first()->pivot->deal_percentage;

        if ((bool) $cap) {
            return min($dealValue, $cap);
        }

        return $dealValue;
    }
}
