<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'pipedriveConfig',
    ];

    protected $appends = [
        'active_integrations',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function customIntegrationFields(): HasMany
    {
        return $this->hasMany(CustomIntegrationField::class);
    }

    public function pipedriveConfig(): HasOne
    {
        return $this->hasOne(PipedriveConfig::class);
    }

    protected function activeIntegrations(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'pipedrive' => $this->pipedriveConfig?->subdomain,
            ]
        );
    }
}
