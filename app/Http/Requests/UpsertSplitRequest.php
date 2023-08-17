<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSplitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'partners' => [
                'array',
            ],

            'partners.*' => [
                'array',
            ],

            'partners.*.id' => [
                'required',
                'exists:agents,id',
            ],

            'partners.*.shared_percentage' => [
                'required',
                'integer',
                'gt:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'partners.*.id.required' => "The partners' identifier field is required.",
            'partners.*.shared_percentage.required' => "The partners' shared percentage field is required.",
            'partners.*.shared_percentage.gt' => "The partners' shared percentage must be greater than 0.",
        ];
    }
}
