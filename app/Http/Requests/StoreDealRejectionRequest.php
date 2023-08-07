<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRejectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rejection_reason' => [
                'required',
                'string',
            ],

            'is_permanent' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason' => 'You must provide a reason.',
        ];
    }
}
