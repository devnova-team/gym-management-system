<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SegmentedMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'phone' => $this['phone'],
            'email' => $this['email'],
            'category' => $this['category'],
            'subscription_end_date' => $this['subscription_end_date'],
            'last_attendance' => $this['last_attendance'],
            'message' => $this['message'],
            'whatsapp_url' => $this['whatsapp_url'],
        ];
    }
}
