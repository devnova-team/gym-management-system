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

        // Lock member to prevent concurrent check-ins.
        $member = $this->attendanceRepository
            ->findMemberForUpdate($gymId, $memberId);

        $today = now('Africa/Cairo')->toDateString();

        // Allow only one check-in per day.
        if ($this->attendanceRepository->hasAttendanceOnDate(
            $member->id,
            $today
        )) {
            throw ValidationException::withMessages([
                'attendance' => [
                    'The member has already checked in today.',
                ],
            ]);
        }

        // Prevent another check-in while still inside.
        if ($this->attendanceRepository->findOpenAttendance($member->id)) {
            throw ValidationException::withMessages([
                'attendance' => [
                    'The member is already checked in.',
                ],
            ]);
        }

                // Get active, paid subscriptions, newest first.
                $subscriptions = $this->attendanceRepository
                    ->getValidSubscriptions($gymId, $member->id, $today);

                $selectedSubscription = $subscriptions->first();

        if ($selectedSubscription) {
            $plan = $selectedSubscription->plan;

            if ($plan->type === 'limited') {
                $usedSessions = $this->attendanceRepository
                    ->countUsedSessions($selectedSubscription->id);

                if ($usedSessions >= $plan->total_sessions) {
                    $selectedSubscription = null;
                }
            }
        }

        if (! $selectedSubscription) {
            throw ValidationException::withMessages([
                'member_id' => [
                    'The member has no active subscription with available sessions.',
                ],
            ]);
        }

        $checkInTime = now();

        return $this->attendanceRepository->create([
            'member_id' => $member->id,
            'subscription_id' => $selectedSubscription->id,
            'check_in_time' => $checkInTime,
            'attendance_date' => $today,
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

    // Process offline records chronologically.
    $sortedRecords = collect($records)
        ->sortBy(function ($record) {
            return CarbonImmutable::parse(
                $record['check_in_time']
            )->utc()->getTimestamp();
        })
        ->values();

    foreach ($sortedRecords as $record) {
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

        $checkInTime = CarbonImmutable::parse(
            $record['check_in_time']
        )->utc();

        $attendanceDate = $checkInTime
            ->setTimezone('Africa/Cairo')
            ->toDateString();

        $member = $this->attendanceRepository
            ->findMemberForUpdate(
                $gymId,
                (int) $record['member_id']
            );

        // Idempotency: return the existing record when identical.
        $existing = $this->attendanceRepository
            ->findByLocalId($gymId, $record['local_id']);

        if ($existing) {
            if (
                (int) $existing->member_id !== (int) $record['member_id']
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

        if ($this->attendanceRepository->localIdExists($record['local_id'])) {
            throw ValidationException::withMessages([
                'local_id' => ['This local ID cannot be used.'],
            ]);
        }

        // Prevent future-dated attendance.
        if ($checkInTime->isFuture()) {
            throw ValidationException::withMessages([
                'check_in_time' => [
                    'Check-in time cannot be in the future.',
                ],
            ]);
        }

        // Only one check-in per Cairo calendar day.
        if ($this->attendanceRepository->hasAttendanceOnDate(
            $member->id,
            $attendanceDate
        )) {
            throw ValidationException::withMessages([
                'attendance' => [
                    'The member has already checked in on this date.',
                ],
            ]);
        }

        // Prevent overlapping or chronologically inconsistent visits.
        if ($this->attendanceRepository->hasAttendanceConflict(
            $member->id,
            $checkInTime
        )) {
            throw ValidationException::withMessages([
                'attendance' => [
                    'Conflicting attendance record exists.',
                ],
            ]);
        }

        // Validate the paid subscription on the original attendance date.
        $subscriptions = $this->attendanceRepository
            ->getValidSubscriptions(
                $gymId,
                $member->id,
                $attendanceDate
            );

        // Newest valid subscription takes priority.
        $subscription = $subscriptions->first();

        if (! $subscription) {
            throw ValidationException::withMessages([
                'member_id' => [
                    'No valid paid subscription on the attendance date.',
                ],
            ]);
        }

        if ($subscription->plan->type === 'limited') {
            $usedSessions = $this->attendanceRepository
                ->countUsedSessions($subscription->id);

            if ($usedSessions >= $subscription->plan->total_sessions) {
                throw ValidationException::withMessages([
                    'member_id' => [
                        'Subscription session limit exceeded.',
                    ],
                ]);
            }
        }

        $attendance = $this->attendanceRepository->create([
            'member_id' => $member->id,
            'subscription_id' => $subscription->id,
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
