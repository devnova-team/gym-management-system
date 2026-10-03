<?php

namespace App\Services;

use App\Repositories\MemberRepository;

class MemberService
{
    protected $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }


    public function getMembers($gym_id, $searchItem = null)
    {

        return $this->memberRepository->getGymMembers($gym_id, $searchItem);
    }


    public function getMemberById($gym_id, $id)
    {

        return $this->memberRepository->findByGymId($gym_id, $id);
    }

    public function createMember($gym_id, array $data)
    {

        $data['gym_id'] = $gym_id;
        return $this->memberRepository->create($data);
    }




    public function updateMember($gym_id, $id, array $data)
    {

        $member = $this->memberRepository->findByGymId($gym_id, $id);
        return $this->memberRepository->update($member, $data);
    }


    public function deleteMember($gym_id, $id)
    {

        $member = $this->memberRepository->findByGymId($gym_id, $id);
        return $this->memberRepository->delete($member);
    }
}
