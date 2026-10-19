<?php

namespace App\Services;

use App\Models\StaffAttendance;
use App\Repositories\StaffAttendanceRepository;
use App\Repositories\StaffRepository;


class StaffAttendanceService
{
    protected $staffAttendanceRepository;
    protected $staffRepository;

    public function __construct(
        StaffAttendanceRepository $staffAttendanceRepository,
        StaffRepository $staffRepository
    ) {
        $this->staffAttendanceRepository = $staffAttendanceRepository;
        $this->staffRepository = $staffRepository;
    }

    public function storeAttendance(int $gym_id, int $staff_id)
    {
        $staff = $this->staffRepository->findByGymId($gym_id, $staff_id);

        $openAttendance = $this->staffAttendanceRepository->findOpenStaffAttendance($staff_id);

        if ($openAttendance) {
            $staffAttendance = $this->staffAttendanceRepository->update(
                $openAttendance,
                ['check_out_time' => now()]
            );
        } else {
            $staffAttendance = $this->staffAttendanceRepository->create(
                [
                    'staff_id' => $staff_id,
                    'check_in_time' => now()
                ]
            );
        }

        return $staffAttendance->load('staff');
    }
}
