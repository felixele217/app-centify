<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'unique:users,email',
            ],

            'base_salary' => [
                'nullable',
                'integer',
            ],

            'on_target_earning' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
