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

            'partners.*.demo_scheduled_deal_percentage' => [
                'nullable',
                'integer',
            ],

            'partners.*.deal_won_deal_percentage' => [
                'nullable',
                'integer',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'partners.*.id.required' => "The partners' identifier field is required.",
        ];
    }
}
