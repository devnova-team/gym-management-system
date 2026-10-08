<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'member' => new MemberResource($this->whenLoaded('member')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
