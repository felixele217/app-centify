<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use App\Enum\AgentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaidLeave extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'continuation_of_pay_time_scope' => ContinuationOfPayTimeScopeEnum::class,
        'reason' => AgentStatusEnum::class,
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
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
