<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'agent';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->orderBy('created_at', 'desc');
    }

    protected function quotaAttainment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->deals->sum('value') / $this->load('plans')->plans->last()->target_amount_per_month
        );
    }

    protected function commission(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quota_attainment * ($this->on_target_earning - $this->base_salary) / 12
        );
    }
}
