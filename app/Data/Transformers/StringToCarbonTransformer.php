<?php

declare(strict_types=1);

namespace App\Data\Transformers;

use Carbon\Carbon;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Transformers\Transformer;

class StringToCarbonTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value): Carbon
    {
        return Carbon::parse($value);
    }
}
