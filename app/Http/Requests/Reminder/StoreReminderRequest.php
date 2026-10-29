<?php

namespace App\Http\Requests\Reminder;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
             'title' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date_format:Y-m-d'],
            'reminder_note' => ['nullable', 'string'],
        ];
    }
}
