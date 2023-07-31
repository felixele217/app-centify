<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\NullZeroNumbersAction;
use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAgentRequest extends FormRequest
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
                'unique:admins,email',
                'unique:agents,email',
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
