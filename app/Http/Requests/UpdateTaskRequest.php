<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'status' => ['sometimes', 'in:pending,in_progress,completed,cancelled'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'عنوان المهمة يجب ألا يتجاوز 255 حرف',
            'due_date.date' => 'تاريخ الاستحقاق غير صالح',
            'priority.in' => 'أولوية المهمة غير صالحة',
            'status.in' => 'حالة المهمة غير صالحة',
            'assigned_to.exists' => 'المستخدم المحدد غير موجود',
            'campaign_id.exists' => 'الحملة المحددة غير موجودة',
        ];
    }
}
