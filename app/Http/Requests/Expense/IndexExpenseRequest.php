<?php

namespace App\Http\Requests\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         $user = $this->user();

        return $user && $user->role === 'owner' && $user->gym_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
            'category' => ['sometimes','required',
                Rule::in(['rent','salaries','bills','maintenance','other']),],

            'from' => ['sometimes','required','date_format:Y-m-d',],

            'to' => ['sometimes','required', 'date_format:Y-m-d',
                Rule::when($this->filled('from'),['after_or_equal:from']),],
            ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Expense category is required.',
            'category.in' => 'Invalid expense category.',

            'from.required' => 'Start date is required.',
            'from.date_format' => 'Start date must use YYYY-MM-DD format.',

            'to.required' => 'End date is required.',
            'to.date_format' => 'End date must use YYYY-MM-DD format.',
            'to.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }


}

