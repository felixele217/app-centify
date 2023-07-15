<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'pipedriveToken',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'active_integrations',
    ];

    public function pipedriveToken(): HasOne
    {
        return $this->hasOne(PipedriveToken::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    protected function activeIntegrations(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'pipedrive' => $this->pipedriveToken ? true : false,
                'salesforce' => false,
            ]
        );
    }
}
