<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\PaidLeave;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DateRangeDoesntOverlapRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $agent = request()->route('agent');

        $agent->paidLeaves->each(function (PaidLeave $paidLeave) use ($fail) {
            if ($this->wrapsExistingPaidLeave($paidLeave)
            || $this->reachesIntoExistingPaidLeave($paidLeave)
            || $this->reachesOutOfExistingPaidLeave($paidLeave)) {
                $fail('You must specify a timeframe that does not overlap with existing paid leaves.');
            }
        });
    }

    private function wrapsExistingPaidLeave(PaidLeave $paidLeave): bool
    {
        return $paidLeave->start_date->gt(request()->get('start_date'))
        && $paidLeave->end_date->lt(request()->get('end_date'));
    }

    private function reachesIntoExistingPaidLeave(PaidLeave $paidLeave): bool
    {
        return $paidLeave->start_date->lt(request()->get('end_date'))
        && $paidLeave->end_date->gt(request()->get('end_date'));
    }

    private function reachesOutOfExistingPaidLeave(PaidLeave $paidLeave): bool
    {
        return  $paidLeave->start_date->lt(request()->get('start_date'))
        && $paidLeave->end_date->gt(request()->get('start_date'));
    }
}
