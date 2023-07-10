<?php

namespace App\Models;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'add_time' => 'datetime',
        'accepted_at' => 'datetime',
        'integration_type' => IntegrationEnum::class,
        'status' => DealStatusEnum::class,
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
