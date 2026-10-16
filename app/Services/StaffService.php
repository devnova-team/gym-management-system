<?php

namespace App\Services;

use App\Repositories\StaffRepository;

class StaffService
{
    protected $staffRepository;

    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }


    public function getStaff(int $gym_id)
    {

        return $this->staffRepository->getGymStaff($gym_id);
    }


    public function getStaffById(int $gym_id, int $id)
    {

        return $this->staffRepository->findByGymId($gym_id, $id);
    }

    public function createStaff(int $gym_id, array $data)
    {

        $data['gym_id'] = $gym_id;
        return $this->staffRepository->create($data);
    }
}
