<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Deal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'add_time' => 'datetime',
        'won_time' => 'datetime',
        'integration_type' => IntegrationTypeEnum::class,
        'status' => DealStatusEnum::class,
    ];

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class)->withPivot([
            'id',
            'deal_percentage',
            'triggered_by',
            'accepted_at',
        ])->using(AgentDeal::class);
    }

    public function SDR(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->agents()->wherePivot('triggered_by', TriggerEnum::DEMO_SET_BY->value)->orderByPivot('created_at')->first(),
        );
    }

    public function AE(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->agents()->wherePivot('triggered_by', TriggerEnum::DEAL_WON->value)->orderByPivot('created_at')->first(),
        );
    }

    public function rejections(): HasMany
    {
        return $this->hasMany(Rejection::class);
    }

    public function activeRejection(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->rejections()->active()->first(),
        );
    }

    public function demoScheduledShareholders(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->agents()->wherePivot('triggered_by', TriggerEnum::DEMO_SET_BY)->orderByPivot('created_at')->get()->skip(1),
        );
    }

    public function dealWonShareholders(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->agents()->wherePivot('triggered_by', TriggerEnum::DEAL_WON)->orderByPivot('created_at')->get()->skip(1),
        );
    }

    public function scopeAccepted(Builder $query, CarbonImmutable $acceptedBefore = null): void
    {
        $acceptedBefore = $acceptedBefore ?? CarbonImmutable::now();

        $query->where('accepted_at', '<', $acceptedBefore);
    }
}
