<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\CustomFieldEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCustomFieldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                new Enum(CustomFieldEnum::class),
            ],

            'api_key' => [
                'required',
                'string',
                'size:40',
            ],
        ];
    }
}
