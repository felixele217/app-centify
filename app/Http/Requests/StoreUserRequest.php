<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            ],

            'base_salary' => [
                'integer',
            ],

            'on_target_earning' => [
                'integer',
            ],
        ];
    }
}
