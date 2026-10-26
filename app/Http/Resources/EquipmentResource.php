<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'last_maintenance_date' => $this->last_maintenance_date?->format('Y-m-d'),
            'next_maintenance_due' => $this->next_maintenance_due?->format('Y-m-d'),
            'maintenance_status' => $this->maintenance_status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
