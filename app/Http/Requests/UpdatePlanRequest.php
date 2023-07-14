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
                'nullable',
                'string',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'target_amount_per_month' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'target_variable' => [
                'nullable',
                new Enum(TargetVariableEnum::class),
            ],

            'payout_frequency' => [
                'nullable',
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

        $data['start_date'] = Carbon::createFromDate($data['start_date']);

        $this->replace($data);
    }

   public function messages(): array
   {
       return [
           'target_amount_per_month.min' => 'The :attribute must be at least 0,01€.',
       ];
   }
}
