<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\AgentStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Actions\NullZeroNumbersAction;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\ContinuationOfPayTimeScopeEnum;

class UpdateAgentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email'),
                Rule::unique('agents', 'email')->ignore(request()->route('agent')->id),
            ],

            'base_salary' => [
                'required',
                'integer',
            ],

            'on_target_earning' => [
                'required',
                'integer',
            ],

            'status' => [
                'required',
                new Enum(AgentStatusEnum::class),
            ],

            'paid_leave' => [
                'array',
            ],

            'paid_leave.start_date' => [
                'required_with:paid_leave',
                'date',
            ],

            'paid_leave.end_date' => [
                'nullable',
                'required_if:status,'.AgentStatusEnum::VACATION->value,
                'date',
                'after:paid_leave.start_date',
            ],

            'paid_leave.continuation_of_pay_time_scope' => [
                'required_with:paid_leave',
                new Enum(ContinuationOfPayTimeScopeEnum::class),
            ],

            'paid_leave.sum_of_commissions' => [
                'required_with:paid_leave',
                'integer',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->replace(NullZeroNumbersAction::execute($this->all(), ['base_salary', 'on_target_earning']));
    }

    public function messages()
    {
        return [
            'paid_leave.end_date.before' => 'The end date needs to come after the start date.',
        ];
    }
}
