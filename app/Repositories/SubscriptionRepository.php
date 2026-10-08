<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository
{

    public function getGymSubscriptions($gym_id)
    {

        return Subscription::whereHas('member', function ($q) use ($gym_id) {
            $q->where('gym_id', $gym_id);
        })->with(['member', 'plan'])->get();
    }


    public function findSubscription($gym_id, $id)
    {


        return  Subscription::whereHas('member', function ($q) use ($gym_id) {
            $q->where('gym_id', $gym_id);
        })->with(['member', 'plan'])->findOrFail($id);
    }


    public function create(array $data)
    {

        return Subscription::create($data);
    }



    public function update(Subscription $subscription, array $data)
    {

        $subscription->update($data);

        return $subscription->fresh(['member', 'plan']);
    }
}
