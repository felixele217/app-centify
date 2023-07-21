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
        return Deal::whereHas('agent.organization', function ($query) {
            $query->where('id', Auth::user()->organization->id);
        })->whereNull('accepted_at')->whereNull('declined_at')->with('agent')->get();
    }
}
