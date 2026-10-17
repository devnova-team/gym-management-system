<?php

namespace App\Repositories;

use App\Models\StaffAttendance;


class StaffAttendanceRepository
{

    public function findOpenStaffAttendance(int $staffId)
    {
        return StaffAttendance::where('staff_id', $staffId)
            ->whereNull('check_out_time')->latest('check_in_time')->first();
    }

    public function create(array $data)
    {
        return StaffAttendance::create($data);
    }

    public function update(StaffAttendance $staffAttendance, array $data)
    {
        $staffAttendance->update($data);
        return $staffAttendance->refresh();
    }
}
