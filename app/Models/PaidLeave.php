<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use App\Enum\AgentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaidLeave extends Model
{
    use HasFactory;
    use HasActiveScope;

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
}
