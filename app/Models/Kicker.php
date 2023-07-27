<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kicker extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => KickerTypeEnum::class,
        'salary_type' => SalaryTypeEnum::class,
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

    protected function payoutInPercent(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
        );
    }
}
