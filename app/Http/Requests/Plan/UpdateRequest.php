<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:daily,3x_week,2x_week',
            'duration_days' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'absence_threshold_days' => 'sometimes|integer|min:0',
        ];
    }



    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            if (count($this->validated()) === 0) {
                $validator->errors()->add('plan', 'At least one field is required.');
            }
        });
    }
}
