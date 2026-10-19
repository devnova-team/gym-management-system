<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    
    public function __construct(
            protected DashboardRepository $dashboardRepository,
            protected MemberSegmentationService $segmentationService
        ) {}

      public function getStats(int $gymId): array
    {
        // Quick statistics
        $activeMembers = $this->dashboardRepository
            ->countActiveMembers($gymId);

        $todayAttendance = $this->dashboardRepository
            ->countTodayAttendance($gymId);

        // Reuse the existing segmentation logic
        $segments = $this->segmentationService
            ->getSegmentedMembers($gymId);

        $segmentCounts = [
            'new' => count($segments['new']),
            'inactive' => count($segments['inactive']),
            'expiring' => count($segments['expiring']),
            'expired' => count($segments['expired']),
        ];

        // Attendance chart data
        $attendanceTrend = $this->dashboardRepository
            ->getAttendanceTrend($gymId);

        return [
            'active_members' => $activeMembers,
            'today_attendance' => $todayAttendance,
            'segmentation' => $segmentCounts,
            'attendance_trend' => $attendanceTrend,
        ];
    }  
}
