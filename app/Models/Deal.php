<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Deal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'add_time' => 'datetime',
        'accepted_at' => 'datetime',
        'integration_type' => IntegrationTypeEnum::class,
        'status' => DealStatusEnum::class,
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function rejections(): HasMany
    {
        return $this->hasMany(Rejection::class);
    }

    public function splits(): HasMany
    {
        return $this->hasMany(Split::class);
    }

    public function activeRejection(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->rejections()->active()->first(),
        );
    }
}
