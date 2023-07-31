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
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = NullZeroNumbersAction::execute($this->all(), ['base_salary', 'on_target_earning']);

        $this->replace($data);
    }
}
