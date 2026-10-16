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


    public function create(array $data)
    {

        return Shift::create($data);
    }
}
