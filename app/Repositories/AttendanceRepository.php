<?php

namespace App\Repositories;
use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Support\Facades\DB;


class AttendanceRepository
{
          public function findMemberForUpdate(int $gymId, int $memberId): Member
    {
        return Member::where('gym_id', $gymId)
            ->lockForUpdate()
            ->findOrFail($memberId);
    } 

        public function findOpenAttendance(int $memberId): ?Attendance
    {
        return Attendance::where('member_id', $memberId)
            ->whereNull('check_out_time')
            ->latest('check_in_time')
            ->first();
    }

        public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

      public function findForGym(
        int $gymId,
        int $attendanceId
    ): Attendance {
        return Attendance::whereHas('member', function ($query) use ($gymId) {
            $query->where('gym_id', $gymId);
        })->findOrFail($attendanceId);
    }
     

  public function findForUpdate(
        int $gymId,
        int $attendanceId
    ): Attendance {
        return Attendance::whereHas('member', function ($query) use ($gymId) {
            $query->where('gym_id', $gymId);
        })->lockForUpdate()->findOrFail($attendanceId);
    }
      
     public function getToday(int $gymId)
    {
        return Attendance::whereDate('attendance_date', today())
            ->whereHas('member', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->with('member')
            ->orderByDesc('check_in_time')
            ->paginate(15);
    }

        public function getCurrentlyInside(int $gymId)
    {
        return Attendance::whereNull('check_out_time')
            ->whereHas('member', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->with('member')
            ->orderByDesc('check_in_time')
            ->paginate(15);
    }


    
}
