<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\PipedriveConfig;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected string $guard = 'admin';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'pipedriveConfig',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'active_integrations',
    ];

    public function pipedriveConfig(): HasOne
    {
        return $this->hasOne(PipedriveConfig::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    protected function activeIntegrations(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'pipedrive' => $this->pipedriveConfig?->refresh_token ? true : false,
                'salesforce' => false,
            ]
        );
    }
}
