<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Services\Commission\DealCommissionService;
use App\Services\Commission\KickerCommissionService;
use App\Services\Commission\PaidLeaveCommissionService;
use App\Services\PaidLeaveDaysService;
use App\Services\QuotaAttainmentChangeService;
use App\Services\QuotaAttainmentService;
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

    protected string $guard = 'agent';

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

    public function sickLeavesDaysCount(): Attribute
    {
        return Attribute::make(
            get: fn () => count((new PaidLeaveDaysService())->paidLeaveDays($this, TimeScopeEnum::MONTHLY, AgentStatusEnum::SICK)),
        );
    }

    public function vacationLeavesDaysCount(): Attribute
    {
        return Attribute::make(
            get: fn () => count((new PaidLeaveDaysService())->paidLeaveDays($this, TimeScopeEnum::MONTHLY, AgentStatusEnum::VACATION)),
        );
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
                $timeScope = request()->query('time_scope') ?? TimeScopeEnum::MONTHLY->value;

                return (new QuotaAttainmentService())->calculate($this, TimeScopeEnum::tryFrom($timeScope));
            }
        );
    }

    protected function quotaAttainmentChange(): Attribute
    {
        return Attribute::make(
            get: function () {
                $timeScope = request()->query('time_scope') ?? TimeScopeEnum::MONTHLY->value;

                return (new QuotaAttainmentChangeService())->calculate($this, TimeScopeEnum::tryFrom($timeScope));
            }
        );
    }

    protected function commission(): Attribute
    {
        $timeScope = request()->query('time_scope') ?? TimeScopeEnum::MONTHLY->value;

        return Attribute::make(
            get: function () use ($timeScope) {
                $dealCommission = (new DealCommissionService())->calculate($this, TimeScopeEnum::tryFrom($timeScope), $this->quotaAttainment);

                $kickerCommission = (new KickerCommissionService())->calculate($this, TimeScopeEnum::tryFrom($timeScope), $this->quotaAttainment);

                $paidLeaveCommission = (new PaidLeaveCommissionService())->calculate($this, TimeScopeEnum::tryFrom($timeScope));

                // dd($dealCommission, $kickerCommission, $paidLeaveCommission);

                return $dealCommission + $kickerCommission + $paidLeaveCommission;
            }
        );
    }
}
