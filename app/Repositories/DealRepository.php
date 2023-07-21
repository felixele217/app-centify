<?php

namespace App\Repositories;

use App\Enum\DealScopeEnum;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DealRepository
{
    public static function get(DealScopeEnum|null $scope): Collection
    {
        $baseQuery = Deal::whereHas('agent.organization', function ($query) {
            $query->where('id', Auth::user()->organization->id);
        });

        return match ($scope) {
            null, DealScopeEnum::ALL => $baseQuery->with('agent')->get(),
            DealScopeEnum::OPEN => $baseQuery->whereNull('accepted_at')->whereNull('declined_at')->with('agent')->get(),
        };
    }
}
