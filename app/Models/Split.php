<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Split extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    protected function sharedPercentage(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
        );
    }
}
