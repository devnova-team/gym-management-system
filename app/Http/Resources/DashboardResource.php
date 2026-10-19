<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    
   public function toArray(Request $request): array
    {
        return [
            'active_members' => $this->resource['active_members'],
            'today_attendance' => $this->resource['today_attendance'],
            'segmentation' => $this->resource['segmentation'],
            'attendance_trend' => $this->resource['attendance_trend'],
        ];
    }
}
