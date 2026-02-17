<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:call,meeting,email,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,critical',
            'client_id' => 'nullable|exists:clients,id',
            'deadline' => 'required|date|after:now',
            'remind_before_minutes' => 'nullable|integer|min:1',
            'remind_via' => 'nullable|string|in:email,system,telegram',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|nullable|string|in:daily,weekly,monthly',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
