<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Services\Commission\PlanKickerCommissionService;
use App\Services\Commission\PlanQuotaCommissionService;
use App\Services\Commission\TotalCommissionService;
use App\Services\Commission\TotalQuotaCommissionChangeService;
use App\Services\PaidLeaveDaysService;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;
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
            get: fn () => $this->plans()->active()->get()->map(function (Plan $plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'quota_attainment' => $quotaAttainment = (new PlanQuotaAttainmentService($this, $plan, queryTimeScope()))->calculate(),
                    'quota_commission' => (new PlanQuotaCommissionService(queryTimeScope()))->calculate($this, $plan),
                    'kicker_commission' => (new PlanKickerCommissionService(queryTimeScope()))->calculate($this, $plan, $quotaAttainment),
                ];
            }),
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
            get: fn () => (new TotalQuotaAttainmentChangeService())->calculate($this, queryTimeScope())
        );
    }

    protected function commission(): Attribute
    {
        return Attribute::make(
            get: fn () => (new TotalCommissionService(queryTimeScope()))->calculate($this)
        );
    }

    protected function commissionChange(): Attribute
    {
        return Attribute::make(
            get: fn () => (new TotalQuotaCommissionChangeService())->calculate($this, queryTimeScope())
        );
    }
}
