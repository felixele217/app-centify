<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\TriggerEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class AgentDeal extends Pivot implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'agent_deal';

    public $incrementing = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'accepted_at' => 'datetime:Y-m-d',
        'triggered_by' => TriggerEnum::class,
    ];

    protected function dealPercentage(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
        );
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
