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
           'phone' => [
            'required',
            'regex:/^01[0125][0-9]{8}$/',
                ],
            'email' => 'nullable|email|max:255',
            'photo_url' => 'nullable|image|mimes:jpg,jpeg,png',
            'join_date' => 'required|date',
        ];
    }
}
