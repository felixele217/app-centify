<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasActiveScope
{
    public function scopeActive(Builder $query): void
    {
        $query->where('start_date', '<', Carbon::now())
            ->where(function ($query) {
                $query->where('end_date', '>', Carbon::now())
                    ->orWhereNull('end_date');
            })
            ->orderBy('start_date', 'desc');
    }
}
