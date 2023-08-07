<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enum\DealScopeEnum;
use App\Enum\TimeScopeEnum;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Rejection;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DealRepository
{
    public static function get(?DealScopeEnum $scope): Collection
    {
        $baseQuery = Deal::with('agent')->whereHas('agent.organization', function (Builder $query) {
            $query->where('id', Auth::user()->organization->id);
        });

        return match ($scope) {
            null => $baseQuery->get(),
            DealScopeEnum::OPEN => $baseQuery->whereNull('accepted_at')->whereNull('declined_at')->get(),
            DealScopeEnum::ACCEPTED => $baseQuery->whereNotNull('accepted_at')->whereNull('declined_at')->get(),
            DealScopeEnum::DECLINED => $baseQuery->whereNull('accepted_at')->whereNotNull('declined_at')->get(),
        };
    }

    public static function dealsForAgent(Agent $agent, TimeScopeEnum $timeScope, CarbonImmutable $dateInScope = null): Collection
    {
        $dateInScope = $dateInScope ?? CarbonImmutable::now();

        $baseQuery = $agent->deals()->whereNotNull('accepted_at')->whereNull('declined_at');

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
            'declined_at' => $hasAcceptedDeal === false ? Carbon::now() : null,
            'note' => $request->validated('note'),
        ]);

        if ($request->validated('rejection_reason')) {
            $deal->rejections()->create([
                'reason' => $request->validated('rejection_reason'),
            ]);
        }
    }
}
