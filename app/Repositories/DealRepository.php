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
        $agentDealsOfOrganization = Deal::with('agents')->whereHas('agents.organization', function (Builder $query) {
            $query->where('id', Auth::user()->organization_id);
        });

        return match ($scope) {
            null => $agentDealsOfOrganization->get(),
            DealScopeEnum::OPEN => $agentDealsOfOrganization->whereHas('agents', function (Builder $query) {
                $query->whereNull('accepted_at');
            })->whereDoesntHave('rejections', function (Builder $query) {
                $query->active();
            })->get(),
            DealScopeEnum::ACCEPTED => $agentDealsOfOrganization->whereHas('agents', function (Builder $query) {
                $query->whereNotNull('accepted_at');
            })->doesntHave('rejections')->get(),
            DealScopeEnum::REJECTED => $agentDealsOfOrganization->whereHas('agents', function (Builder $query) {
                $query->whereNull('accepted_at');
            })->whereHas('rejections', function (Builder $query) {
                $query->active();
            })->get(),
        };
    }

    public static function dealsForAgent(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $baseQuery = $agent->deals()->whereHas('agents', function (Builder $query) {
            $query->whereNotNull('agent_deal.accepted_at');
        });

        $deals = collect();

        $activePlans = $agent->plans()->active($dateInScope)->get();

        [$firstDateInScope, $lastDateInScope] = DateHelper::firstAndLastDateInScope($dateInScope ?? CarbonImmutable::now(), $timeScope);
        foreach ($activePlans as $plan) {
            $currentQuery = clone $baseQuery;

            switch ($plan->trigger) {
                case TriggerEnum::DEMO_SCHEDULED:
                    $currentQuery = $currentQuery->whereBetween('add_time', [$firstDateInScope, $lastDateInScope])->whereHas('agents', function (Builder $query) use ($agent) {
                        $query->where('agent_deal.agent_id', $agent->id)->where('agent_deal.triggered_by', TriggerEnum::DEMO_SCHEDULED->value);
                    });
                    break;
                case TriggerEnum::DEAL_WON:
                    $currentQuery = $currentQuery->whereBetween('won_time', [$firstDateInScope, $lastDateInScope])->whereHas('agents', function (Builder $query) use ($agent) {
                        $query->where('agent_deal.agent_id', $agent->id)->where('agent_deal.triggered_by', TriggerEnum::DEAL_WON->value);
                    });
                    break;
            }

            $deals = $deals->concat($currentQuery->get());
        }

        return $deals->unique('id');
    }

    public static function update(Deal $deal, UpdateDealRequest $request): void
    {
        if (array_key_exists('note', $request->validated())) {
            $deal->update(['note' => $request->validated('note')]);
        }

        if (array_key_exists('has_accepted_deal', $request->validated())) {
            if ($deal->won_time) {
                $deal->ae?->pivot->update([
                    'accepted_at' => $request->validated('has_accepted_deal') === true ? Carbon::now() : null,
                ]);
            } else {
                $deal->sdr?->pivot->update([
                    'accepted_at' => $request->validated('has_accepted_deal') === true ? Carbon::now() : null,
                ]);
            }
        }
    }
}
