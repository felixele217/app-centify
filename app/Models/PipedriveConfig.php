<?php

declare(strict_types=1);

namespace App\Models;

use App\Encrypter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipedriveConfig extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
        'expires_at',
    ];

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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
