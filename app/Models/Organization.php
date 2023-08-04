<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Integration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function integrations(): HasMany
    {
      return $this->hasMany(Integration::class);
    }

    public function pipedriveConfig(): HasOne
    {
        return $this->hasOne(PipedriveConfig::class);
    }

    protected function activeIntegrations(): Attribute
    {
        return Attribute::make(
            get: function () {
                $activeIntegrations = [];

                if ($this->pipedriveConfig?->subdomain) {
                    $activeIntegrations['pipedrive'] = $this->pipedriveConfig?->subdomain;
                }

                return $activeIntegrations;
            }
        );
    }
}
