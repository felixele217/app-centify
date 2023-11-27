<?php

declare(strict_types=1);

namespace App\Models;

use App\Encrypter;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Integration extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'name' => IntegrationTypeEnum::class,
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    protected function accessToken(): Attribute
    {
        return Attribute::make(
            get: fn (string $access_token) => Encrypter::decrypt($access_token),
            set: fn (string $access_token) => Encrypter::encrypt($access_token),
        );
    }

    protected function refreshToken(): Attribute
    {
        return Attribute::make(
            get: fn (string $refresh_token) => Encrypter::decrypt($refresh_token),
            set: fn (string $refresh_token) => Encrypter::encrypt($refresh_token),
        );
    }
}
