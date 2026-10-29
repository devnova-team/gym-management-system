<?php

namespace App\Http\Requests\Financial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FinancialSummaryRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
             'from' => [
                'required',
                'date_format:Y-m-d',
            ],

            'to' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],
        ];
    }
}
