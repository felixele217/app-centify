<?php

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Services\Commission\DealCommissionService;
use App\Services\Commission\KickerCommissionService;
use App\Services\Commission\PaidLeaveCommissionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected $appends = [
        'status',
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

    public function activePaidLeave(): HasOne
    {
        return $this->hasOne(PaidLeave::class)->active();
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->load('activePaidLeave')->activePaidLeave?->reason->value === AgentStatusEnum::SICK->value) {
                    return AgentStatusEnum::SICK;
                }

                if ($this->activePaidLeave?->reason->value === AgentStatusEnum::VACATION->value) {
                    return AgentStatusEnum::VACATION;
                }

                return AgentStatusEnum::ACTIVE;
            }
        );
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
            get: function () use ($timeScope) {
                $dealCommission = (new DealCommissionService())->calculate($this, TimeScopeEnum::tryFrom($timeScope));
                $kickerCommission = (new KickerCommissionService())->calculate($this);

                $paidLeaveCommission = (new PaidLeaveCommissionService())->calculate($this, TimeScopeEnum::tryFrom($timeScope));

                return $dealCommission + $kickerCommission + $paidLeaveCommission;
            }
        );
    }
}
