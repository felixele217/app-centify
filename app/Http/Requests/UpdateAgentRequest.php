<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\NullZeroNumbersAction;
use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
                'nullable',
                'required_with:paid_leave.end_date,paid_leave.continuation_of_pay_time_scope,paid_leave.sum_of_commissions',
                'date',
            ],

            'paid_leave.end_date' => [
                'nullable',
                'date',
                'required_if:status,'.AgentStatusEnum::VACATION->value,
                'after:paid_leave.start_date',
            ],

            'paid_leave.continuation_of_pay_time_scope' => [
                'nullable',
                'required_with:paid_leave.end_date,paid_leave.start_date,paid_leave.sum_of_commissions',
                new Enum(ContinuationOfPayTimeScopeEnum::class),
            ],

            'paid_leave.sum_of_commissions' => [
                'nullable',
                'required_with:paid_leave.end_date,paid_leave.start_date,paid_leave.continuation_of_pay_time_scope',
                'integer',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = NullZeroNumbersAction::execute($this->all(), ['base_salary', 'on_target_earning']);

        if (isset($data['paid_leave'])) {
            $data['paid_leave'] = NullZeroNumbersAction::execute($data['paid_leave'], ['sum_of_commissions']);
        }

        $this->replace($data);
    }

    public function messages(): array
    {
        return [
            'paid_leave.end_date.before' => 'The end date needs to come after the start date.',
        ];
    }
}
