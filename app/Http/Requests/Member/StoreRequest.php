<?php

namespace App\Http\Requests\Member;

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
            'phone' => 'required|string|max:11',
            'email' => 'nullable|email|max:255',
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
            'join_date' => 'required|date',
        ];
    }
}
