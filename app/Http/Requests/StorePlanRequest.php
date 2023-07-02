<?php

namespace App\Http\Requests;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePlanRequest extends FormRequest
{
    public function rules(): array
    {
    //    dd($this->all());
        return [
            'name' => [
                'required',
                'string',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'target_amount_per_month' => [
                'required',
                'integer',
                'min:1'
            ],

            'target_variable' => [
                'required',
                new Enum(TargetVariableEnum::class),
            ],

            'payout_frequency' => [
                'required',
                new Enum(PayoutFrequencyEnum::class),
            ],

            'assigned_agents' => [
                'array',
            ],

            'assigned_agents.*' => [
                'exists:users,id',
            ],

        ];
    }
}
