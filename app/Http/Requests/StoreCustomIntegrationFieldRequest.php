<?php

namespace App\Http\Requests;

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCustomIntegrationFieldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                new Enum(CustomIntegrationFieldEnum::class),
            ],

            'api_key' => [
                'required',
                'string',
                'size:40',
            ],

            'integration_type' => [
                'required',
                new Enum(IntegrationTypeEnum::class),
            ],
        ];
    }
}
