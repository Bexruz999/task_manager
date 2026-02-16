<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TaskUpdateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(TaskStatus::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Поле статус обязательно для заполнения.',
            'status.Illuminate\\Validation\\Rules\\Enum' => 'Выбранный статус недействителен. (Допустимые значения: pending, in_progress, done, cancelled)',
        ];
    }
}
