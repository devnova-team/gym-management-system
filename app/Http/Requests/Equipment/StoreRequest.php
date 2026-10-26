<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date'
        ];
    }
}
