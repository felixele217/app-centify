<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Organization;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'name' => IntegrationTypeEnum::class,
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
