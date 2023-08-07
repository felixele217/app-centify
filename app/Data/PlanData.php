<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\Transformers\StringToCarbonTransformer;
use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;

class PlanData extends Data
{
    public function __construct(
        public string $name,
        #[Date]
        #[WithTransformer(StringToCarbonTransformer::class)]
        public string $start_date,
        public int $target_amount_per_month,
        public TargetVariableEnum $target_variable,
        public PayoutFrequencyEnum $payout_frequency,
        public array $assigned_agent_ids,
        public ?CliffData $cliff,
        public ?KickerData $kicker,
        public ?int $cap,
    ) {
    }

    public static function messages(): array
    {
        return [
            'target_amount_per_month.min' => 'The :attribute must be at least 0,01€.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'cliff.time_scope' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
            'cliff.threshold_in_percent' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ];
    }
}
