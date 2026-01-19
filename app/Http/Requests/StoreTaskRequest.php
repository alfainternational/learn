<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المهمة مطلوب',
            'title.max' => 'عنوان المهمة يجب ألا يتجاوز 255 حرف',
            'due_date.date' => 'تاريخ الاستحقاق غير صالح',
            'priority.required' => 'أولوية المهمة مطلوبة',
            'priority.in' => 'أولوية المهمة غير صالحة',
            'status.required' => 'حالة المهمة مطلوبة',
            'status.in' => 'حالة المهمة غير صالحة',
            'assigned_to.exists' => 'المستخدم المحدد غير موجود',
            'campaign_id.exists' => 'الحملة المحددة غير موجودة',
        ];
    }
}
