<?php

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    protected AttendanceRepository $attendanceRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function checkIn(int $gymId, int $memberId): Attendance
    {
        return DB::transaction(function () use ($gymId, $memberId) {

            // Lock the member to prevent concurrent check-ins.
            $member = $this->attendanceRepository
                ->findMemberForUpdate($gymId, $memberId);

            $today = today()->toDateString();

            // The member must have at least one active, paid subscription.
            $hasValidSubscription = $member->subscriptions()
                ->where('payment_status', 'paid')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('plan', function ($query) use ($gymId) {
                    $query->where('gym_id', $gymId);
                })
                ->exists();

            if (! $hasValidSubscription) {
                throw ValidationException::withMessages([
                    'member_id' => [
                        'The member does not have an active, paid subscription.',
                    ],
                ]);
            }

            // Do not allow another visit while the member is still inside.
            $openAttendance = $this->attendanceRepository
                ->findOpenAttendance($member->id);

            if ($openAttendance) {
                throw ValidationException::withMessages([
                    'attendance' => [
                        'The member is already checked in.',
                    ],
                ]);
            }

            $checkInTime = now();

            return $this->attendanceRepository->create([
                'member_id' => $member->id,
                'check_in_time' => $checkInTime,
                'attendance_date' => $checkInTime->toDateString(),
            ]);
        });
    }

    public function checkOut(
        int $gymId,
        int $attendanceId
    ): Attendance {
        return DB::transaction(function () use ($gymId, $attendanceId) {

            $attendance = $this->attendanceRepository
                ->findForUpdate($gymId, $attendanceId);

            if ($attendance->check_out_time !== null) {
                throw ValidationException::withMessages([
                    'attendance' => [
                        'This attendance record is already checked out.',
                    ],
                ]);
            }

            $attendance->check_out_time = now();
            $attendance->save();

            return $attendance->fresh(['member']);
        });
    }

    public function getToday(int $gymId)
    {
        return $this->attendanceRepository->getToday($gymId);
    }

    public function getCurrentlyInside(int $gymId)
    {
        return $this->attendanceRepository->getCurrentlyInside($gymId);
    }
}
