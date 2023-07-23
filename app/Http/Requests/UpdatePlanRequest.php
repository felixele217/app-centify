<?php

namespace App\Http\Requests;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePlanRequest extends FormRequest
{
    public function rules(): array
    {
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
                'min:1',
            ],

            'target_variable' => [
                'required',
                new Enum(TargetVariableEnum::class),
            ],

            'payout_frequency' => [
                'required',
                new Enum(PayoutFrequencyEnum::class),
            ],

            'assigned_agent_ids' => [
                'array',
            ],

            'assigned_agent_ids.*' => [
                'exists:agents,id',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (isset($data['start_date'])) {
            $data['start_date'] = Carbon::createFromDate($data['start_date']);
        }

        $this->replace($data);
    }

    public function messages(): array
    {
        return [
            'target_amount_per_month.min' => 'The :attribute must be at least 0,01€.',
        ];
    }
}
