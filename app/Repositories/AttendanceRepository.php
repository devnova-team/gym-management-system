<?php

namespace App\Repositories;
use App\Models\Attendance;
use App\Models\Member;



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
        return Attendance::whereDate('attendance_date', now('Africa/Cairo')->toDateString())
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

  
public function findByLocalId(
    int $gymId,
    string $localId
): ?Attendance {
    return Attendance::query()
        ->where('local_id', $localId)
        ->whereHas('member', function ($query) use ($gymId) {
            $query->where('gym_id', $gymId);
        })
        ->first();
}


// public function hasAttendanceAfter(
//     int $memberId,
//     \DateTimeInterface $checkInTime
// ): bool {
//     return Attendance::query()
//         ->where('member_id', $memberId)
//         ->where('check_in_time', '>=', $checkInTime)
//         ->exists();
// }


public function hasAttendanceConflict(
    int $memberId,
    \DateTimeInterface $checkInTime
): bool {
    return Attendance::query()
        ->where('member_id', $memberId)
        ->where(function ($query) use ($checkInTime) {
            $query
                // A visit started at or after the offline check-in.
                ->where('check_in_time', '>=', $checkInTime)

                // Or a previous visit was still active at that time.
                ->orWhere(function ($query) use ($checkInTime) {
                    $query
                        ->where('check_in_time', '<', $checkInTime)
                        ->where(function ($query) use ($checkInTime) {
                            $query
                                ->whereNull('check_out_time')
                                ->orWhere(
                                    'check_out_time',
                                    '>',
                                    $checkInTime
                                );
                        });
                });
        })
        ->exists();
}


public function localIdExists(string $localId): bool
{
    return Attendance::where('local_id', $localId)->exists();
}
    
}
