<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:draft,active,paused,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'اسم الحملة يجب ألا يتجاوز 255 حرف',
            'start_date.date' => 'تاريخ البداية غير صالح',
            'end_date.date' => 'تاريخ النهاية غير صالح',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'budget.numeric' => 'الميزانية يجب أن تكون رقماً',
            'budget.min' => 'الميزانية يجب أن تكون قيمة موجبة',
            'status.in' => 'حالة الحملة غير صالحة',
        ];
    }
}
