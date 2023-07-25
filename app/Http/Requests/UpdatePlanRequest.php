<?php

namespace App\Http\Requests;

use App\Enum\KickerTypeEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Enum\SalaryTypeEnum;
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
                'present',
            ],

            'assigned_agent_ids.*' => [
                'exists:agents,id',
            ],

            'cliff_threshold_in_percent' => [
                'nullable',
                'integer',
            ],

            'kicker' => [
                'array',
            ],

            'kicker.type' => [
                'string',
                new Enum(KickerTypeEnum::class),
                'required_with:kicker,kicker.threshold_in_percent,kicker.payout_in_percent,kicker.salary_type',
            ],

            'kicker.threshold_in_percent' => [
                'integer',
                'min:1',
                'required_with:kicker,kicker.type,kicker.payout_in_percent,kicker.salary_type',

            ],

            'kicker.payout_in_percent' => [
                'integer',
                'min:1',
                'required_with:kicker,kicker.type,kicker.threshold_in_percent,kicker.salary_type',
            ],

            'kicker.salary_type' => [
                'string',
                new Enum(SalaryTypeEnum::class),
                'required_with:kicker,kicker.type,kicker.threshold_in_percent,kicker.payout_in_percent',
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
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.type' => 'Please specify all fields for the Kicker.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker.',
        ];
    }
}
