<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminReminderResource extends JsonResource
{
   
    public function toArray(Request $request): array
    {
        return[
           
           'id' => $this->id,
            'title' => $this->title,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'reminder_note' => $this->reminder_note,
            'status' => $this->due_date->lt(
                now('Africa/Cairo')->startOfDay()
            ) ? 'due' : (
                $this->due_date->isSameDay(
                    now('Africa/Cairo')
                ) ? 'due' : 'upcoming'
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
