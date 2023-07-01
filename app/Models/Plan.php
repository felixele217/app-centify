<?php

namespace App\Models;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\RoleEnum;
use App\Enum\TargetVariableEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'target_variable' => TargetVariableEnum::class,
        'payout_frequency' => PayoutFrequencyEnum::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function agents(): BelongsToMany
    {
        return $this->users()
            ->whereHas('roles', function ($query) {
                $query->where('name', RoleEnum::AGENT->value);
            });
    }
}
