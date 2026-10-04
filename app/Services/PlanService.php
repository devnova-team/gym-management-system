<?php

namespace App\Services;

use App\Repositories\PlanRepository;

class PlanService
{
    protected $PlanRepository;

    public function __construct(PlanRepository $PlanRepository)
    {
        $this->PlanRepository = $PlanRepository;
    }


    public function getPlans($gym_id)
    {

        return $this->PlanRepository->getGymPlans($gym_id);
    }


    public function getPlanById($gym_id, $id)
    {

        return $this->PlanRepository->findByGymId($gym_id, $id);
    }

    public function createPlan($gym_id, array $data)
    {

        $data['gym_id'] = $gym_id;
        return $this->PlanRepository->create($data);
    }




    public function updatePlan($gym_id, $id, array $data)
    {

        $Plan = $this->PlanRepository->findByGymId($gym_id, $id);
        return $this->PlanRepository->update($Plan, $data);
    }


    public function deletePlan($gym_id, $id)
    {

        $Plan = $this->PlanRepository->findByGymId($gym_id, $id);
        return $this->PlanRepository->delete($Plan);
    }
}
