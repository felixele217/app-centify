<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\DealScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DealRepository
{
    public static function get(DealScopeEnum $scope = null): Collection
    {
        $agentDealsOfOrganization = Deal::with('agent')->whereHas('agent.organization', function (Builder $query) {
            $query->where('id', Auth::user()->organization->id);
        });

        return match ($scope) {
            null => $agentDealsOfOrganization->get(),
            DealScopeEnum::OPEN => $agentDealsOfOrganization->whereNull('accepted_at')->whereDoesntHave('rejections', function (Builder $query) {
                $query->active();
            })->get(),
            DealScopeEnum::ACCEPTED => $agentDealsOfOrganization->whereNotNull('accepted_at')->doesntHave('rejections')->get(),
            DealScopeEnum::DECLINED => $agentDealsOfOrganization->whereNull('accepted_at')->whereHas('rejections', function (Builder $query) {
                $query->active();
            })->get(),
        };
    }

    public static function dealsForAgent(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $dateInScope = $dateInScope ?? CarbonImmutable::now();

        $baseQuery = $agent->deals()->whereNotNull('accepted_at')->doesntHave('rejections');

        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => $monthlyDealsQuery = $baseQuery->whereMonth('add_time', $dateInScope->month)->get(),
            TimeScopeEnum::QUARTERLY => $baseQuery->whereBetween('add_time', [$dateInScope->firstOfQuarter(), $dateInScope->endOfQuarter()])->get(),
            TimeScopeEnum::ANNUALY => $baseQuery->whereBetween('add_time', [$dateInScope->firstOfYear(), $dateInScope->lastOfYear()])->get(),
            default => $monthlyDealsQuery
        };
    }

    public static function update(Deal $deal, UpdateDealRequest $request): void
    {
        $hasAcceptedDeal = $request->validated('has_accepted_deal');

        $deal->update([
            'accepted_at' => $hasAcceptedDeal === true ? Carbon::now() : null,
            'note' => $request->validated('note'),
        ]);
    }
}
