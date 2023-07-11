<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Plan;
use App\Enum\TimeScopeEnum;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->belongsToMany(Plan::class);
    }

    public function currentPlan(): Plan
    {
        return $this->plans()->orderBy('created_at', 'desc')->first();
    }

    protected function quotaAttainment(): Attribute
    {
        $timeScope = request()->query('time_scope');

        return Attribute::make(
            get: fn () => match ($timeScope) {
                TimeScopeEnum::MONTHLY->value => $this->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $this->load('plans')->plans->first()->target_amount_per_month,
                TimeScopeEnum::QUARTERLY->value => $this->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfQuarter(), Carbon::now()->endOfQuarter()])->sum('value') / $this->load('plans')->plans->last()->target_amount_per_month * 3,
                TimeScopeEnum::ANNUALY->value => $this->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()])->sum('value') / $this->load('plans')->plans->last()->target_amount_per_month * 12,
            }
        );
    }

    protected function commission(): Attribute
    {
        $timeScope = request()->query('time_scope');

        return Attribute::make(
            get: fn () => match ($timeScope) {
                TimeScopeEnum::MONTHLY->value => $this->quota_attainment * ($this->on_target_earning - $this->base_salary) / 12,
                TimeScopeEnum::QUARTERLY->value => $this->quota_attainment * ($this->on_target_earning - $this->base_salary) / 4,
                TimeScopeEnum::ANNUALY->value => $this->quota_attainment * ($this->on_target_earning - $this->base_salary),
            }
        );
    }
}
