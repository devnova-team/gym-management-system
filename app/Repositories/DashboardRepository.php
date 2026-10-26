<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    
    public function countActiveMembers(int $gymId): int
    {
        $today = Carbon::today('Africa/Cairo')->toDateString();

        return Member::query()
            ->where('gym_id', $gymId)
            ->whereHas('subscriptions', function ($query) use ($today) {
                $query->where('payment_status', 'paid')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->count();
    }

     public function countTodayAttendance(int $gymId): int
    {
        $today = Carbon::today('Africa/Cairo')->toDateString();

        return Attendance::query()
            ->whereHas('member', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->whereDate('attendance_date', $today)
            ->distinct()
            ->count('member_id');
    }


    public function getAttendanceTrend(int $gymId): array
    {
        $startDate = Carbon::today('Africa/Cairo')->subDays(6);
        $endDate = Carbon::today('Africa/Cairo');

        $attendance = Attendance::query()
            ->whereHas('member', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->whereBetween('attendance_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->select(
                'attendance_date',
                DB::raw('COUNT(DISTINCT member_id) as total')
            )
            ->groupBy('attendance_date')
            ->pluck('total', 'attendance_date');

        $trend = [];

        for ($date = $startDate->copy();
             $date->lte($endDate);
             $date->addDay()) {

            $day = $date->toDateString();

            $trend[] = [
                'date' => $day,
                'total' => (int) ($attendance[$day] ?? 0),
            ];
        }

        return $trend;
    }
}
