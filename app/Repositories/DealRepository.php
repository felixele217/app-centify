<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\DealScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
        $baseQuery = $agent->deals()->whereNotNull('accepted_at');

        $deals = collect();

        $activePlans = $agent->plans()->active($dateInScope)->get();

        [$firstDateInScope, $lastDateInScope] = DateHelper::firstAndLastDateInScope($dateInScope ?? CarbonImmutable::now(), $timeScope);
        foreach ($activePlans as $plan) {
            $currentQuery = clone $baseQuery;

            switch ($plan->trigger) {
                case TriggerEnum::DEMO_SET_BY:
                    $currentQuery = $currentQuery->whereBetween('add_time', [$firstDateInScope, $lastDateInScope]);
                    break;
                case TriggerEnum::DEAL_WON:
                    $currentQuery = $currentQuery->whereBetween('won_time', [$firstDateInScope, $lastDateInScope]);
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
