<?php

declare(strict_types=1);

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
}
