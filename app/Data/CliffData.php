<?php

declare(strict_types=1);

namespace App\Data;

use App\Enum\TimeScopeEnum;
use Spatie\LaravelData\Attributes\Validation\RequiredWith;
use Spatie\LaravelData\Data;

class CliffData extends Data
{
    public function __construct(
        #[RequiredWith('threshold_in_percent')]
        public TimeScopeEnum $time_scope,

        public ?int $threshold_in_percent,
    ) {
    }
}
