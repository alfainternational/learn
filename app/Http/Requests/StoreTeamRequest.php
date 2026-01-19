<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
            'description' => ['nullable', 'string'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفريق مطلوب',
            'name.max' => 'اسم الفريق يجب ألا يتجاوز 255 حرف',
            'name.unique' => 'اسم الفريق موجود مسبقاً',
            'leader_id.exists' => 'قائد الفريق المحدد غير موجود',
            'members.array' => 'أعضاء الفريق يجب أن تكون قائمة',
            'members.*.exists' => 'أحد الأعضاء المحددين غير موجود',
        ];
    }
}
