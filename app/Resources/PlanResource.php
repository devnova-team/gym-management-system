<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'duration_days' => $this->duration_days,
            'price' => $this->price,
            'absence_threshold_days' => $this->absence_threshold_days,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
