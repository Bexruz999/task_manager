<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'sometimes|string|in:call,meeting,email,other',
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|string',
            'priority' => 'sometimes|string|in:low,medium,high,critical',
            'deadline' => 'sometimes|date',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_type' => 'required_if:is_recurring,true|nullable|string|in:daily,weekly,monthly',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
