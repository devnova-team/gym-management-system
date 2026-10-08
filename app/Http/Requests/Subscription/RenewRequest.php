<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class RenewRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => 'required|integer|exists:subscriptions,id',
            'plan_id' => 'required|integer|exists:plans,id',
            'payment_status' => 'required|string',
            'payment_method' => 'required|in:cash',
        ];
    }
}
