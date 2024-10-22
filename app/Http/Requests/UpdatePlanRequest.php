<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\KickerTypeEnum;
use App\Enum\PlanCycleEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TargetVariableEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
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

            'trigger' => [
                'required',
                new Enum(TriggerEnum::class),
            ],

            'plan_cycle' => [
                'required',
                new Enum(PlanCycleEnum::class),
            ],

            'assigned_agents' => [
                'present',
                'array',
            ],

            'assigned_agents.*.id' => [
                'exists:agents,id',
            ],

            'assigned_agents.*.share_of_variable_pay' => [
                'integer',
                'gt:0',
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

            'kicker.time_scope' => [
                'nullable',
                'string',
                new Enum(TimeScopeEnum::class),
                'required_with:kicker.type,kicker.threshold_in_percent,kicker.payout_in_percent,kicker.salary_type',
            ],

            'cap' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (isset($data['start_date'])) {
            $data['start_date'] = Carbon::createFromDate($data['start_date'])->startOfDay();
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
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'cliff.time_scope' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
            'cliff.threshold_in_percent' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
            'assigned_agents.0.share_of_variable_pay' => 'The share of variable pay of all assigned agents must be greater than 0.',
        ];
    }
}
