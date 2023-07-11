<?php

namespace App\Models;

use App\Enum\TimeScopeEnum;
use Carbon\Carbon;
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
        return $this->belongsToMany(Plan::class);
    }

    public function activePlans(): BelongsToMany
    {
        return $this->plans()
            ->where('start_date', '<', Carbon::now())
            ->where(function ($query) {
                $query->where('end_date', '>', Carbon::now())
                    ->orWhereNull('end_date');
            });
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
