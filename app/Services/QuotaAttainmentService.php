<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Split;
use App\Repositories\DealRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class QuotaAttainmentService
{
    private CarbonImmutable $dateInScope;

    public function __construct(CarbonImmutable $dateInScope = null)
    {
        $this->dateInScope = $dateInScope ?? CarbonImmutable::now();
    }

    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?float
    {
        $latestActivePlan = $agent->load('plans')->plans()->active($this->dateInScope)->first();

        if (! $latestActivePlan) {
            return null;
        }

        $deals = DealRepository::dealsForAgent($agent, $timeScope, $this->dateInScope);

        return $this->cappedSumOfDeals($deals, $latestActivePlan) / ($latestActivePlan->target_amount_per_month * $timeScope->monthCount());
    }

    private function cappedSumOfDeals(Collection $deals, ?Plan $latestActivePlan): float
    {
        return array_sum($deals->map(fn (Deal $deal) => $this->cappedValue($deal, $latestActivePlan->cap?->value))->toArray());
    }

    private function cappedValue(Deal $deal, ?int $cap): float
    {
        $dealValue = $this->splittedValue($deal);

        if ((bool) $cap && $dealValue >= $cap) {
            return $cap;
        }

        return $dealValue;
    }

    private function splittedValue(Deal $deal): float
    {
        $sharedPercentage = array_sum($deal->load('splits')->splits?->map(fn (Split $split) => $split->shared_percentage)->toArray());

        return max(
            $deal->value * (1 - $sharedPercentage),
            0
        );
    }
}
