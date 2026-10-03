<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
