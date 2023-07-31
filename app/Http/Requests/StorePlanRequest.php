<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\NullZeroNumbersAction;
use App\Enum\KickerTypeEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TimeScopeEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePlanRequest extends FormRequest
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

            'cliff' => [
                'array',
            ],

            'cliff.time_scope' => [
                'nullable',
                'string',
                new Enum(TimeScopeEnum::class),
                'required_with:cliff.threshold_in_percent',
            ],

            'cliff.threshold_in_percent' => [
                'nullable',
                'integer',
                'required_with:cliff.time_scope',
            ],

            'kicker' => [
                'array',
            ],

            'kicker.type' => [
                'nullable',
                'string',
                new Enum(KickerTypeEnum::class),
                'required_with:kicker.threshold_in_percent,kicker.payout_in_percent,kicker.salary_type',
            ],

            'kicker.threshold_in_percent' => [
                'nullable',
                'integer',
                'required_with:kicker.type,kicker.payout_in_percent,kicker.salary_type',
            ],

            'kicker.payout_in_percent' => [
                'nullable',
                'integer',
                'required_with:kicker.type,kicker.threshold_in_percent,kicker.salary_type',
            ],

            'kicker.salary_type' => [
                'nullable',
                'string',
                new Enum(SalaryTypeEnum::class),
                'required_with:kicker.type,kicker.threshold_in_percent,kicker.payout_in_percent',
            ],

            'cap' => [
                'integer',
                'min:1',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (isset($data['start_date'])) {
            $data['start_date'] = Carbon::createFromDate($data['start_date']);
        }

        if (isset($data['kicker'])) {
            $data['kicker'] = NullZeroNumbersAction::execute($data['kicker'], ['payout_in_percent', 'threshold_in_percent']);
        }
       
        if (isset($data['cliff'])) {
            $data['cliff'] = NullZeroNumbersAction::execute($data['cliff'], ['threshold_in_percent']);
        }

        $this->replace($data);
    }

    public function messages(): array
    {
        return [
            'target_amount_per_month.min' => 'The :attribute must be at least 0,01€.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'cliff.time_scope' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
            'cliff.threshold_in_percent' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ];
    }
}
