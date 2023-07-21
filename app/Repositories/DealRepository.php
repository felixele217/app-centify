<?php

namespace App\Repositories;

use App\Enum\DealScopeEnum;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DealRepository
{
    public static function get(DealScopeEnum|null $scope): Collection
    {
        $baseQuery = Deal::with('agent')->whereHas('agent.organization', function ($query) {
            $query->where('id', Auth::user()->organization->id);
        });

        return match ($scope) {
            null => $baseQuery->get(),
            DealScopeEnum::OPEN => $baseQuery->whereNull('accepted_at')->whereNull('declined_at')->get(),
            DealScopeEnum::ACCEPTED => $baseQuery->whereNotNull('accepted_at')->whereNull('declined_at')->get(),
            DealScopeEnum::DECLINED => $baseQuery->whereNull('accepted_at')->whereNotNull('declined_at')->get(),
        };
    }

    public static function update(Deal $deal, UpdateDealRequest $request): void
    {
        $hasAcceptedDeal = $request->validated('has_accepted_deal');

        $deal->update([
            'accepted_at' => $hasAcceptedDeal === true ? Carbon::now() : null,
            'declined_at' => $hasAcceptedDeal === false ? Carbon::now() : null,
        ]);
    }
}
