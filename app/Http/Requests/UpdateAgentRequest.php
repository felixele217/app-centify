<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'gt:base_salary',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'on_target_earning.gt' => 'The :attribute must be greater than the base salary.',
        ];
    }
}
