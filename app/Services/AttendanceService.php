<?php

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;

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

            //$today = today()->toDateString();
            $today = now('Africa/Cairo')->toDateString();

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
                'attendance_date' => $checkInTime
                    ->copy()
                    ->setTimezone('Africa/Cairo')
                    ->toDateString(),
                //'attendance_date' => $checkInTime->toDateString(),
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




    public function sync(int $gymId, array $records): array
    {
        $results = [];

        foreach ($records as $record) {
            try {
                $results[] = $this->syncRecord($gymId, $record);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                $results[] = [
                    'local_id' => $record['local_id'],
                    'status' => 'rejected',
                    'message' => 'Member not found.',
                ];
            } catch (ValidationException $e) {
                $results[] = [
                    'local_id' => $record['local_id'],
                    'status' => 'rejected',
                    'message' => collect($e->errors())
                        ->flatten()
                        ->first(),
                ];
            } catch (UniqueConstraintViolationException $e) {
                $results[] = [
                    'local_id' => $record['local_id'],
                    'status' => 'rejected',
                    'message' => 'Attendance sync conflict. Please retry.',
                ];
            }
        }

        return $results;
    }




    private function syncRecord(int $gymId, array $record): array
    {
        return DB::transaction(function () use ($gymId, $record) {

            // Convert the original check-in time to UTC.
            $checkInTime = CarbonImmutable::parse(
                $record['check_in_time']
            )->utc();

            // Calculate attendance date using Egypt's timezone.
            $attendanceDate = $checkInTime
                ->setTimezone('Africa/Cairo')
                ->toDateString();

            // Lock the member and verify gym ownership.
            $member = $this->attendanceRepository
                ->findMemberForUpdate(
                    $gymId,
                    (int) $record['member_id']
                );

            // Check whether this record was already synchronized.
            $existing = $this->attendanceRepository
                ->findByLocalId($gymId, $record['local_id']);

            if ($existing) {
                if (
                    $existing->member_id !== (int) $record['member_id']
                    || ! $existing->check_in_time->equalTo($checkInTime)
                ) {
                    throw ValidationException::withMessages([
                        'local_id' => [
                            'This local ID belongs to a different attendance record.',
                        ],
                    ]);
                }

                return [
                    'local_id' => $record['local_id'],
                    'status' => 'already_synced',
                    'attendance_id' => $existing->id,
                ];
            }

            // Reject a local ID already used by another gym.
            if (
                $this->attendanceRepository
                ->localIdExists($record['local_id'])
            ) {
                throw ValidationException::withMessages([
                    'local_id' => [
                        'This local ID cannot be used.',
                    ],
                ]);
            }

            // Validate subscription at the original attendance date.
            $hasValidSubscription = $member->subscriptions()
                ->where('payment_status', 'paid')
                ->whereDate('start_date', '<=', $attendanceDate)
                ->whereDate('end_date', '>=', $attendanceDate)
                ->whereHas('plan', function ($query) use ($gymId) {
                    $query->where('gym_id', $gymId);
                })
                ->exists();

            if (! $hasValidSubscription) {
                throw ValidationException::withMessages([
                    'member_id' => [
                        'No valid paid subscription at the time of check-in.',
                    ],
                ]);
            }

            // Prevent conflicting attendance records.
            // if (
            //     $this->attendanceRepository
            //         ->findOpenAttendance($member->id)
            //     || $this->attendanceRepository
            //         ->hasAttendanceAfter($member->id, $checkInTime)
            // ) {
            //     throw ValidationException::withMessages([
            //         'attendance' => [
            //             'Conflicting attendance record exists.',
            //         ],
            //     ]);
            // }

            if (
                $this->attendanceRepository->hasAttendanceConflict(
                    $member->id,
                    $checkInTime
                )
            ) {
                throw ValidationException::withMessages([
                    'attendance' => [
                        'Conflicting attendance record exists.',
                    ],
                ]);
            }

            // Save the offline attendance record.
            $attendance = $this->attendanceRepository->create([
                'member_id' => $member->id,
                'check_in_time' => $checkInTime,
                'attendance_date' => $attendanceDate,
                'local_id' => $record['local_id'],
            ]);

            return [
                'local_id' => $record['local_id'],
                'status' => 'synced',
                'attendance_id' => $attendance->id,
            ];
        });
    }
}
