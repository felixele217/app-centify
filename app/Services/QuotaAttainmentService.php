<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotaAttainmentService
{
    private CarbonImmutable $dateInScope;

    public function __construct(CarbonImmutable $dateInScope = null)
    {
        $this->dateInScope = $dateInScope ?? new CarbonImmutable();
    }

    public function calculate(Agent $agent, TimeScopeEnum $timeScope): float
    {
        $latestActivePlan = $agent->load('plans')->plans()->active()->first();

        if (! $latestActivePlan) {
            return 0;
        }

        $dealsQuery = match ($timeScope) {
            TimeScopeEnum::MONTHLY => $monthlyDealsQuery = $agent->deals()->whereMonth('accepted_at', $this->dateInScope->month),
            TimeScopeEnum::QUARTERLY => $agent->deals()->whereBetween('accepted_at', [$this->dateInScope->firstOfQuarter(), $this->dateInScope->endOfQuarter()]),
            TimeScopeEnum::ANNUALY => $agent->deals()->whereBetween('accepted_at', [$this->dateInScope->firstOfYear(), $this->dateInScope->lastOfYear()]),
            default => $monthlyDealsQuery
        };

        return $this->cappedSumOfDeals($dealsQuery, $latestActivePlan) / ($latestActivePlan->target_amount_per_month * $timeScope->monthCount());
    }

    private function cappedSumOfDeals(HasMany $dealsQuery, ?Plan $latestActivePlan): int
    {
        return array_sum($dealsQuery->get()->map(fn (Deal $deal) => $this->cappedValue($deal, $latestActivePlan->cap?->value))->toArray());
    }

    private function cappedValue(Deal $deal, ?int $cap): int
    {
        if ((bool) $cap && $deal->value >= $cap) {
            return $cap;
        }

        return $deal->value;
    }
}
