<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class SyncAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'records' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],

            'records.*.local_id' => [
                'required',
                'uuid',
                'distinct',
            ],

            'records.*.member_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'records.*.check_in_time' => [
                'required',
                'date_format:Y-m-d\TH:i:sP',
                'before_or_equal:now',
            ],
        ];
    }
}
