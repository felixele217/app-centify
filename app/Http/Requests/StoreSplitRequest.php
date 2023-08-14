<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSplitRequest extends FormRequest
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
            ],
        ];
    }
}
