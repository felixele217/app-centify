<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rejection extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
