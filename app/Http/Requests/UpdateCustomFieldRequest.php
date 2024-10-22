<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomFieldRequest extends FormRequest
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
