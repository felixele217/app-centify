<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PaidLeave extends Model implements Auditable
{
    use HasActiveScope;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
