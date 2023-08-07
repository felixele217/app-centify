<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function rules(): array
    {
        // dd($this->all());
        return [
            'has_accepted_deal' => [
                'boolean',
            ],

            'note' => [
                'nullable',
                'string',
            ],

            'rejection_reason' => [
                'nullable',
                'string',
               'required_without_all:note,has_accepted_deal'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason' => 'You must provide a reason.'
        ];
    }
}
