<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomIntegrationFieldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'api_key' => [
                'required',
                'string',
                'size:40',
            ],
        ];
    }
}
