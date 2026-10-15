<?php

namespace App\Http\Requests\MessageTemplates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMessageTemplateRequest extends FormRequest
{
    /**
     * Determine if the authenticated user can update templates.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category' => [
                'required',
                'string',
                Rule::in([
                    'new',
                    'inactive',
                    'expiring',
                    'expired',
                ]),
            ],
            'template_text' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }
}
