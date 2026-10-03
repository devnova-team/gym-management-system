<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository
{

    public function getGymMembers($gym_id, $searchItem = null)
    {

        $query = Member::where('gym_id', $gym_id);

        if (!empty($searchItem)) {
            $query->where(function ($subquery) use ($searchItem) {
                $subquery->where('name', 'like', '%' . $searchItem . '%')
                    ->orWhere('phone', 'like', '%' . $searchItem . '%');
            });
        }

        return $query->paginate(10);
    }


    public function findByGymId($gym_id, $id)
    {

        return  Member::where('gym_id', $gym_id)->findOrFail($id);
    }


    public function create(array $data)
    {

        return Member::create($data);
    }



    public function update(Member $member, array $data)
    {

        $member->update($data);
        $member->refresh();
        return $member;
    }


    public function delete(Member $member)
    {
        return $member->delete();
    }
}
