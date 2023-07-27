<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plan extends Model
{
    use HasFactory;
    use HasActiveScope;

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

    public function cliff(): HasOne
    {
        return $this->hasOne(Cliff::class);
    }

    public function kicker(): HasOne
    {
        return $this->hasOne(Kicker::class);
    }
}
