<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class QuotaAttainmentPerTriggerService
{
    private CarbonImmutable $dateInScope;

    public function __construct(
        private Agent $agent,
        private TriggerEnum $trigger,
        private TimeScopeEnum $timeScope,
        CarbonImmutable $dateInScope = null
    ) {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(): ?float
    {
        $latestActivePlan = $this->agent->load('plans')->plans()->active($this->dateInScope)->first();

        if (! $latestActivePlan) {
            return null;
        }

        $deals = DealRepository::dealsForAgent($this->agent, $this->timeScope, $this->dateInScope);
        // dd($deals->count());

        $deals = $deals->filter(function (Deal $deal) {
            // dd($deal->agents()->whereAgentId($this->agent->id)->first());
            return $deal->agents()->whereAgentId($this->agent->id)->wherePivot('triggered_by', $this->trigger)->exists();
        });

        return $this->cappedSumOfDeals($deals, $latestActivePlan) / ($latestActivePlan->target_amount_per_month * $this->timeScope->monthCount());
    }

    private function cappedSumOfDeals(Collection $deals, ?Plan $latestActivePlan): float
    {
        return array_sum($deals->map(fn (Deal $deal) => $this->cappedValue($deal, $latestActivePlan->cap?->value))->toArray());
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
