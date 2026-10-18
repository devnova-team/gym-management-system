<?php

namespace App\Services;

use App\Repositories\ShiftRepository;
use App\Repositories\StaffRepository;
use Illuminate\Validation\ValidationException;


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

        if ($this->shiftRepository->hasConflict($staff_id, $data)) {

            throw ValidationException::withMessages([
                'shift' => ['This staff member already has a shift that overlaps with the selected time.']
            ]);
        }


        $data['staff_id'] = $staff->id;

        $shift = $this->shiftRepository->create($data);
        return $shift->load('staff');
    }
}
