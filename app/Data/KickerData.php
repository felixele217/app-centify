<?php

declare(strict_types=1);

namespace App\Data;

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use Spatie\LaravelData\Attributes\Validation\RequiredWith;
use Spatie\LaravelData\Data;

class KickerData extends Data
{
    public function __construct(
        #[RequiredWith('salary_type', 'payout_in_percent', 'threshold_in_percent')]
        public ?KickerTypeEnum $type,

        #[RequiredWith('salary_type', 'payout_in_percent', 'type')]
        public ?int $threshold_in_percent,

        #[RequiredWith('salary_type', 'threshold_in_percent', 'type')]
        public ?int $payout_in_percent,

        #[RequiredWith('payout_in_percent', 'threshold_in_percent', 'type')]
        public ?SalaryTypeEnum $salary_type,

        #[RequiredWith('salary_type', 'payout_in_percent', 'threshold_in_percent', 'type')]
        public ?TimeScopeEnum $time_scope,
    ) {
    }
}
