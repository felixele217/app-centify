<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'has_accepted_deal' => [
                'boolean',
            ],
        ];
    }
}