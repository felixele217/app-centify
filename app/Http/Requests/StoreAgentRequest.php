<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'email',
                'unique:admins,email',
                'unique:agents,email',
            ],

            'base_salary' => [
                'nullable',
                'integer',
            ],

            'on_target_earning' => [
                'nullable',
                'integer',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach (['base_salary', 'on_target_earning'] as $field) {
            $data[$field] = $data[$field] === 0 ? null : $data[$field];
        }

        $this->replace($data);
    }
}
