<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyPlanAgentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'agent_id' => [
                'required',
                'exists:agents,id',
            ],
        ];
    }
}
