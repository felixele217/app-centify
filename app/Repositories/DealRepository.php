<?php

declare(strict_types=1);

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Agent;
use App\Enum\TriggerEnum;
use App\Enum\DealScopeEnum;
use App\Enum\TimeScopeEnum;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateDealRequest;
use Illuminate\Database\Eloquent\Builder;

class DealRepository
{
    public static function get(DealScopeEnum $scope = null): Collection
    {
        $agentDealsOfOrganization = Deal::with('agent')->whereHas('agent.organization', function (Builder $query) {
            $query->where('id', Auth::user()->organization_id);
        });

        return match ($scope) {
            null => $agentDealsOfOrganization->get(),
            DealScopeEnum::OPEN => $agentDealsOfOrganization->whereNull('accepted_at')->whereDoesntHave('rejections', function (Builder $query) {
                $query->active();
            })->get(),
            DealScopeEnum::ACCEPTED => $agentDealsOfOrganization->whereNotNull('accepted_at')->doesntHave('rejections')->get(),
            DealScopeEnum::REJECTED => $agentDealsOfOrganization->whereNull('accepted_at')->whereHas('rejections', function (Builder $query) {
                $query->active();
            })->get(),
        };
    }

    public static function dealsForAgent(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $dateInScope = $dateInScope ?? CarbonImmutable::now();

        $baseQuery = $agent->deals()->whereNotNull('accepted_at');

        $deals = collect([]);

        $activePlans = $agent->plans()->active($dateInScope)->get();

        foreach ($activePlans as $plan) {
            $currentQuery = clone $baseQuery;
            switch ($plan->trigger) {
                case TriggerEnum::DEMO_SET_BY:
                    $currentQuery = match ($timeScope) {
                        TimeScopeEnum::MONTHLY => $currentQuery->whereMonth('add_time', $dateInScope->month),
                        TimeScopeEnum::QUARTERLY => $currentQuery->whereBetween('add_time', [$dateInScope->firstOfQuarter(), $dateInScope->endOfQuarter()]),
                        TimeScopeEnum::ANNUALY => $currentQuery->whereBetween('add_time', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()]),
                        default => $currentQuery->whereMonth('add_time', $dateInScope->month)
                    };
                    break;
                case TriggerEnum::DEAL_WON:
                    $currentQuery = match ($timeScope) {
                        TimeScopeEnum::MONTHLY => $currentQuery->whereMonth('won_time', $dateInScope->month),
                        TimeScopeEnum::QUARTERLY => $currentQuery->whereBetween('won_time', [$dateInScope->firstOfQuarter(), $dateInScope->endOfQuarter()]),
                        TimeScopeEnum::ANNUALY => $currentQuery->whereBetween('won_time', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()]),
                        default => $currentQuery->whereMonth('won_time', $dateInScope->month)
                    };
                    break;
            }
            $deals = $deals->concat($currentQuery->get());
        }

        return $deals->unique();
    }

    public static function update(Deal $deal, UpdateDealRequest $request): void
    {
        if (array_key_exists('note', $request->validated())) {
            $deal->update(['note' => $request->validated('note')]);
        }

        if (array_key_exists('has_accepted_deal', $request->validated())) {
            $deal->update(['accepted_at' => $request->validated('has_accepted_deal') === true ? Carbon::now() : null]);
        }
    }
}
