<?php

namespace App\Models;

use App\Enum\AgentStatusEnum;
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
        'status' => AgentStatusEnum::class,
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

    public function paidLeaves(): HasMany
    {
        return $this->hasMany(PaidLeave::class);
    }

    protected function quotaAttainment(): Attribute
    {
        return Attribute::make(
            get: function () {
                $timeScope = request()->query('time_scope');

                $latestPlanTargetAmountPerMonth = $this->load('plans')->plans()->active()->first()?->target_amount_per_month;

                if (! $latestPlanTargetAmountPerMonth) {
                    return 0;
                }

                return match ($timeScope) {
                    TimeScopeEnum::MONTHLY->value => $this->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $latestPlanTargetAmountPerMonth,
                    TimeScopeEnum::QUARTERLY->value => $this->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfQuarter(), Carbon::now()->endOfQuarter()])->sum('value') / ($latestPlanTargetAmountPerMonth * 3),
                    TimeScopeEnum::ANNUALY->value => $this->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()])->sum('value') / ($latestPlanTargetAmountPerMonth * 12),
                    default => $this->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $latestPlanTargetAmountPerMonth,
                };
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
