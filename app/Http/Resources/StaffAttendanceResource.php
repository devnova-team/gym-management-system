<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class StaffAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'staff_id' => $this->staff_id,
            'staff_name' => $this->whenLoaded('staff', fn() => $this->staff->name),
            'check_in_time' => $this->check_in_time?->format('Y-m-d H:i:s'),
            'check_out_time' => $this->check_out_time?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
