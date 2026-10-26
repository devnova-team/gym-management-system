<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class AttendanceResource extends JsonResource
     {
         public function toArray(Request $request): array
    {
        $durationMinutes = null;

        if ($this->check_in_time && $this->check_out_time) {
            $durationMinutes = (int) $this->check_in_time
                ->diffInMinutes($this->check_out_time);
        }

        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'subscription_id' => $this->subscription_id,
            'check_in_time' => $this->check_in_time?->format('Y-m-d H:i:s'),
            'check_out_time' => $this->check_out_time?->format('Y-m-d H:i:s'),
            'attendance_date' => $this->attendance_date?->format('Y-m-d'),
            'duration_minutes' => $durationMinutes,
            'status' => $this->check_out_time ? 'checked_out' : 'inside',

            'member' => new MemberResource(
                $this->whenLoaded('member')
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
    
            }
