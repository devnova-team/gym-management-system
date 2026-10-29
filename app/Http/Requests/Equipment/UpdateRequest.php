<?php

namespace App\Http\Requests\Equipment;

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
            'name' => 'sometimes|required|string|max:255',
            'purchase_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date'
        ];
    }


    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            $fields = $this->only([
                'name',
                'purchase_date',
                'last_maintenance_date',
                'next_maintenance_due',
            ]);

            if (empty($fields)) {
                $validator->errors()->add('name', 'At least one field is required.');
            }
        });
    }
}
