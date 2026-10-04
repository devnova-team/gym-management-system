<?php

namespace App\Http\Requests\Plan;

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
            'type' => 'required|string|in:daily,3x_week,2x_week',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'absence_threshold_days' => 'required|integer|min:0',
        ];
    }
}
