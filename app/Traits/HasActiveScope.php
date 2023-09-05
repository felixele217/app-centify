<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

trait HasActiveScope
{
    public function scopeActive(Builder $query, CarbonImmutable $dateInScope = null): void
    {
        $dateInScope = $dateInScope ?? CarbonImmutable::now();

        $query->where('start_date', '<=', $dateInScope)
            ->where(function ($query) use ($dateInScope) {
                $query->where('end_date', '>', $dateInScope)
                    ->orWhereNull('end_date');
            })
            ->orderBy('start_date', 'desc');
    }
}
