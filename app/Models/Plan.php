<?php

namespace App\Models;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'target_variable' => TargetVariableEnum::class,
        'payout_frequency' => PayoutFrequencyEnum::class,
        'start_date' => 'datetime:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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
