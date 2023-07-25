<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliff extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
