<?php

namespace App\Repositories;

use App\Models\Equipment;

class EquipmentRepository
{

    public function getGymEquipment(int $gym_id)
    {

        return Equipment::where('gym_id', $gym_id)->orderBy('next_maintenance_due')->get();
    }


    public function findByGymId(int $gym_id, int $id)
    {

        return Equipment::where('gym_id', $gym_id)->findOrFail($id);
    }


    public function create(array $data)
    {

        return Equipment::create($data);
    }


    public function update(Equipment $equipment, array $data)
    {

        $equipment->update($data);
        $equipment->refresh();
        return $equipment;
    }
}
