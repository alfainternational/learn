<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'target_audience' => ['nullable', 'array'],
        ];
    }
}
