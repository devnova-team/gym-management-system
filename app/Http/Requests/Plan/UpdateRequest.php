<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;



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
            'type' => 'sometimes|in:limited,unlimited',
            'duration_days' => 'sometimes|integer|min:1',
            'total_sessions' => 'sometimes|nullable|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'absence_threshold_days' => 'sometimes|integer|min:0',
        ];
    }



     public function after(): array
{
    return [
        function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $data = $validator->validated();

            if (empty($data)) {
                $validator->errors()->add(
                    'plan',
                    'At least one field is required.'
                );
                return;
            }

            $plan = \App\Models\Plan::query()
                ->where('gym_id', $this->user()->gym_id)
                ->find($this->route('id'));

            if (!$plan) {
                return;
            }

            $type = $data['type'] ?? $plan->type;

            $sessions = array_key_exists('total_sessions', $data)
                ? $data['total_sessions']
                : $plan->total_sessions;

            if ($type === 'limited' && $sessions === null) {
                $validator->errors()->add(
                    'total_sessions',
                    'Total sessions is required for limited plans.'
                );
            }

            if ($type === 'unlimited' && $sessions !== null) {
                // Switching from limited to unlimited is allowed 
                // PlanService will clear the old session limit.
                if (($data['type'] ?? null) !== 'unlimited'
                    || array_key_exists('total_sessions', $data)) {
                    $validator->errors()->add(
                        'total_sessions',
                        'Unlimited plans cannot have a session limit.'
                    );
                }
            }
        },
    ];
}
}
