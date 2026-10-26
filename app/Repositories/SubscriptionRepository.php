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

        public function getLatestMemberSubscription(int $gymId, int $memberId): ?Subscription {
    return Subscription::query()
        ->where('member_id', $memberId)
        ->whereHas('member', function ($query) use ($gymId) {
            $query->where('gym_id', $gymId);
        })
        ->with('plan')
        ->orderByDesc('id')
        ->first();
}

public function countUsedSessions(int $subscriptionId): int
{
    return \App\Models\Attendance::query()
        ->where('subscription_id', $subscriptionId)
        ->count();
}

public function findMemberForUpdate(int $gymId, int $memberId): \App\Models\Member
{
    return \App\Models\Member::query()
        ->where('gym_id', $gymId)
        ->lockForUpdate()
        ->findOrFail($memberId);
}
}
