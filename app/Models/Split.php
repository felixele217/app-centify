<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Split extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function scopeAcceptedDeals(Builder $query, CarbonImmutable $acceptedBefore = null): void
    {
        $acceptedBefore = $acceptedBefore ?? CarbonImmutable::now();

        $query->whereHas('deal', function (Builder $query) use ($acceptedBefore) {
            $query->where('accepted_at', '<', $acceptedBefore);
        });
    }

    protected function sharedPercentage(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
        );
    }
}
