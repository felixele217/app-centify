<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Services\Commission\PaidLeaveCommissionService;
use App\Services\Commission\TotalKickerCommissionService;
use App\Services\Commission\TotalQuotaCommissionChangeService;
use App\Services\Commission\TotalQuotaCommissionService;
use App\Services\PaidLeaveDaysService;
use App\Services\QuotaAttainment\TotalQuotaAttainmentChangeService;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class Agent extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

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

    protected $with = [
        'organization',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(Deal::class)->withPivot([
            'id',
            'deal_percentage',
            'triggered_by',
            'accepted_at',
        ])->using(AgentDeal::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->withPivot([
            'id',
            'share_of_variable_pay',
        ])->using(AgentPlan::class);
    }

    public function paidLeaves(): HasMany
    {
        return $this->hasMany(PaidLeave::class)->orderBy('start_date');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function activePaidLeave(): HasOne
    {
        return $this->hasOne(PaidLeave::class)->active();
    }

    public function sickLeavesDaysCount(): Attribute
    {
        return Attribute::make(
            get: fn () => count((new PaidLeaveDaysService())->paidLeaveDays($this, queryTimeScope(), AgentStatusEnum::SICK)),
        );
    }

    public function vacationLeavesDaysCount(): Attribute
    {
        return Attribute::make(
            get: fn () => count((new PaidLeaveDaysService())->paidLeaveDays($this, queryTimeScope(), AgentStatusEnum::VACATION)),
        );
    }

    protected function variablePay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->on_target_earning - $this->base_salary
        );
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->load('activePaidLeave')->activePaidLeave?->reason->value) {
                    AgentStatusEnum::SICK->value => AgentStatusEnum::SICK,
                    AgentStatusEnum::VACATION->value => AgentStatusEnum::VACATION,
                    default => AgentStatusEnum::ACTIVE
                };
            }
        );
    }

    public function activePlans(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->plans()->active()->select('plans.id', 'plans.name')->get(),
        );
    }

    protected function quotaAttainment(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (new TotalQuotaAttainmentService($this, queryTimeScope()))->calculate();
            }
        );
    }

    protected function quotaAttainmentChange(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (new TotalQuotaAttainmentChangeService())->calculate($this, queryTimeScope());
            }
        );
    }

    protected function commission(): Attribute
    {
        $timeScope = queryTimeScope();

        return Attribute::make(
            get: function () use ($timeScope) {
                $quotaCommission = (new TotalQuotaCommissionService($timeScope))->calculate($this);

                $totalKickerCommission = (new TotalKickerCommissionService())->calculate($this, $timeScope);

                $paidLeaveCommission = (new PaidLeaveCommissionService())->calculate($this, $timeScope);

                return $quotaCommission + $totalKickerCommission + $paidLeaveCommission;
            }
        );
    }

    protected function commissionChange(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (new TotalQuotaCommissionChangeService())->calculate($this, queryTimeScope());
            }
        );
    }
}
