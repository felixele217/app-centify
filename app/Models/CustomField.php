<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Integration;
use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomField extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'name' => CustomFieldEnum::class,
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
