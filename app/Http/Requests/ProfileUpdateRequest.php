<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\Agent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => [
                'email',
                'max:255',
                Rule::unique(Admin::class)->ignore($this->user()->id),
                Rule::unique(Agent::class)->ignore($this->user()->id),
            ],
        ];
    }
}
