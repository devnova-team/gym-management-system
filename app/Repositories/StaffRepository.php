<?php

namespace App\Repositories;

use App\Models\User;

class StaffRepository
{

    public function getGymStaff(int $gym_id)
    {

        return User::where('gym_id', $gym_id)->get();
    }


    public function findByGymId(int $gym_id, int $id)
    {
        return User::where('gym_id', $gym_id)->findOrFail($id);
    }


    public function create(array $data)
    {

        return User::create($data);
    }
}
