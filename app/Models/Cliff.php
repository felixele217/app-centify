<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\TimeScopeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliff extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'time_scope' => TimeScopeEnum::class,
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    protected function thresholdInPercent(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
        );
    }
}
