<?php

namespace App\Services;

use App\Repositories\ShiftRepository;
use App\Repositories\StaffRepository;


class ShiftService
{
    protected $shiftRepository;
    protected $staffRepository;

    public function __construct(ShiftRepository $shiftRepository, StaffRepository $staffRepository)
    {
        $this->shiftRepository = $shiftRepository;
        $this->staffRepository = $staffRepository;
    }


    public function getShifts(int $gym_id)
    {

        return $this->shiftRepository->getGymShifts($gym_id);
    }


    public function createShift(int $gym_id, int $staff_id, array $data)
    {

        $staff = $this->staffRepository->findByGymId($gym_id, $staff_id);
        $data['staff_id'] = $staff->id;

        $shift = $this->shiftRepository->create($data);
        return $shift->load('staff');
    }
}
