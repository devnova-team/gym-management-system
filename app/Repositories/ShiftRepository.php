<?php

namespace App\Repositories;

use App\Models\Shift;

class ShiftRepository
{

    public function getGymShifts(int $gym_id)
    {

        return Shift::whereHas('staff', function ($q) use ($gym_id) {
            $q->where('gym_id', $gym_id);
        })->with('staff')->get();
    }


    public function hasConflict(int $staff_id, array $data)
    {
        return Shift::where('staff_id', $staff_id)
            ->whereDate('shift_date', $data['shift_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])->exists();
    }

    public function create(array $data)
    {

        return Shift::create($data);
    }
}
