<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository
{

    public function getGymPlans($gym_id)
    {

        return Plan::where('gym_id', $gym_id)->get();
    }


    public function findByGymId($gym_id, $id)
    {

        return  Plan::where('gym_id', $gym_id)->findOrFail($id);
    }


    public function create(array $data)
    {

        return Plan::create($data);
    }



    public function update(Plan $Plan, array $data)
    {

        $Plan->update($data);
        $Plan->refresh();
        return $Plan;
    }


    public function delete(Plan $Plan)
    {
        return $Plan->delete();
    }
}
