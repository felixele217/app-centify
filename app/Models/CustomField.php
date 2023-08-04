<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomField extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'name' => CustomFieldEnum::class,
        'integration_type' => IntegrationTypeEnum::class,
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
