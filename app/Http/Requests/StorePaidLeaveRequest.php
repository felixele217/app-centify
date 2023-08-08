<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePaidLeaveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => [
                'required',
                new Enum(AgentStatusEnum::class),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'required_if:reason,'.AgentStatusEnum::VACATION->value,
                'after:start_date',
            ],

            'continuation_of_pay_time_scope' => [
                'required',
                new Enum(ContinuationOfPayTimeScopeEnum::class),
            ],

            'sum_of_commissions' => [
                'required',
                'integer',
            ],

            'employed_28_or_more_days' => [
                'required_if:reason,sick',
                'accepted',
                'exclude',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.before' => 'The end date needs to come after the start date.',
            'employed_28_or_more_days' => 'The employee has to be employed for 28 or more days.',
        ];
    }
}
