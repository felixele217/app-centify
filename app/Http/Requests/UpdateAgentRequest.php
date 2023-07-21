<?php

namespace App\Http\Requests;

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
                'nullable',
                'string',
            ],

            'email' => [
                'nullable',
                'email',
                Rule::unique('admins', 'email'),
                Rule::unique('agents', 'email')->ignore(request()->route('agent')->id),
            ],

            'base_salary' => [
                'nullable',
                'integer',
            ],

            'on_target_earning' => [
                'nullable',
                'integer',
            ],

            'status' => [
                'nullable',
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
                'required_if:status,'.AgentStatusEnum::VACATION->value,
                'date',
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
        $data = $this->all();

        foreach (['base_salary', 'on_target_earning'] as $field) {
            $data[$field] = $data[$field] === 0 ? null : $data[$field];
        }

        $this->replace($data);
    }
}
