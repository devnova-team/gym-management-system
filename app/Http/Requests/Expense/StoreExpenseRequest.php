<?php

namespace App\Http\Requests\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user
            && $user->role === 'owner'
            && $user->gym_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'category' => ['required',
                Rule::in(['rent','salaries','bills','maintenance','other'])],

            'amount' => ['required','numeric','gt:0','decimal:0,2','max:99999999.99'],

            'expense_date' => ['required','date_format:Y-m-d'],

            'notes' => ['nullable','string','max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Expense category is required.',
            'category.in' => 'Invalid expense category.',
            'amount.required' => 'Expense amount is required.',
            'amount.numeric' => 'Amount must be numeric.',
            'amount.gt' => 'Amount must be greater than zero.',
            'amount.decimal' => 'Amount can have up to 2 decimal places.',
            'expense_date.required' => 'Expense date is required.',
            'expense_date.date_format' => 'Date must use YYYY-MM-DD format.',
            'notes.max' => 'Notes may not exceed 500 characters.',
        ];
    }


}

