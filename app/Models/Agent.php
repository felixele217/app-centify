<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\AgentStatusEnum;
use App\Services\Commission\CommissionChangeService;
use App\Services\Commission\CommissionFromQuotaService;
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
use OwenIt\Auditing\Contracts\Auditable;

class Agent extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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

    protected $with = [
        'organization',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'demo_set_by_agent_id');
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class);
    }

    public function paidLeaves(): HasMany
    {
        return $this->hasMany(PaidLeave::class)->orderBy('start_date');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(Split::class);
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
                return (new QuotaAttainmentService())->calculate($this, queryTimeScope());
            }
        );
    }

    protected function quotaAttainmentChange(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (new QuotaAttainmentChangeService())->calculate($this, queryTimeScope());
            }
        );
    }

    protected function commission(): Attribute
    {
        $timeScope = queryTimeScope();

        return Attribute::make(
            get: function () use ($timeScope) {
                $commissionFromQuota = (new CommissionFromQuotaService())->calculate($this, $timeScope, $this->quotaAttainment);

                $kickerCommission = (new KickerCommissionService())->calculate($this, $timeScope, $this->quotaAttainment);

                $paidLeaveCommission = (new PaidLeaveCommissionService())->calculate($this, $timeScope);

                return $commissionFromQuota + $kickerCommission + $paidLeaveCommission;
            }
        );
    }

    protected function commissionChange(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (new CommissionChangeService())->calculate($this, queryTimeScope());
            }
        );
    }
}
