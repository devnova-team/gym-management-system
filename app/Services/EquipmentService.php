<?php

namespace App\Services;

use App\Repositories\EquipmentRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class EquipmentService
{

    protected $equipmentRepository;

    public function __construct(EquipmentRepository $equipmentRepository)
    {
        $this->equipmentRepository = $equipmentRepository;
    }

    public function getEquipment(int $gym_id)
    {

        return $this->equipmentRepository->getGymEquipment($gym_id);
    }



    public function createEquipment(int $gym_id, array $data)
    {

        $this->validateMaintenanceDates(
            $data['last_maintenance_date'] ?? null,
            $data['next_maintenance_due'] ?? null
        );

        $data['gym_id'] = $gym_id;
        return $this->equipmentRepository->create($data);
    }


    public function updateEquipment(int $gym_id, int $id, array $data)
    {

        $equipment = $this->equipmentRepository->findByGymId($gym_id, $id);

        $last = array_key_exists('last_maintenance_date', $data)
            ? $data['last_maintenance_date']
            : $equipment->last_maintenance_date;

        $next = array_key_exists('next_maintenance_due', $data)
            ? $data['next_maintenance_due']
            : $equipment->next_maintenance_due;

        $this->validateMaintenanceDates($last, $next);

        return $this->equipmentRepository->update($equipment, $data);
    }

    private function validateMaintenanceDates($last, $next)
    {

        if ($last && $next && Carbon::parse($next)->lt(Carbon::parse($last))) {

            throw ValidationException::withMessages([

                'next_maintenance_due' => ['The next maintenance date must be on or after the last maintenance date.']
            ]);
        }
    }
}
