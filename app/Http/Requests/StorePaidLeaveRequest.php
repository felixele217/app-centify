<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\NullZeroNumbersAction;
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
                'nullable',
                'required_with:end_date,continuation_of_pay_time_scope,sum_of_commissions',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'required_if:reason,'.AgentStatusEnum::VACATION->value,
                'after:start_date',
            ],

            'continuation_of_pay_time_scope' => [
                'nullable',
                'required_with:end_date,start_date,sum_of_commissions',
                new Enum(ContinuationOfPayTimeScopeEnum::class),
            ],

            'sum_of_commissions' => [
                'nullable',
                'required_with:end_date,start_date,continuation_of_pay_time_scope',
                'integer',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = NullZeroNumbersAction::execute($this->all(), ['sum_of_commissions']);

        $this->replace($data);
    }

    public function messages(): array
    {
        return [
            'end_date.before' => 'The end date needs to come after the start date.',
        ];
    }
}
