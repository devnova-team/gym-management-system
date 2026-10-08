<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;
use App\Repositories\PlanRepository;
use App\Repositories\MemberRepository;
use Carbon\Carbon;

class SubscriptionService
{
    protected $subscriptionRepository;
    protected $planRepository;
    protected $memberRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, PlanRepository $planRepository,   MemberRepository $memberRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->planRepository = $planRepository;
        $this->memberRepository = $memberRepository;
    }


    public function getSubscriptions($gym_id)
    {

        return $this->subscriptionRepository->getGymSubscriptions($gym_id);
    }


    public function findSubscription($gym_id, $id)
    {

        return $this->subscriptionRepository->findSubscription($gym_id, $id);
    }

    public function createSubscription($gym_id, $member_id, $plan_id, array $data)
    {

        $member = $this->memberRepository->findByGymId($gym_id, $member_id);
        $plan = $this->planRepository->findByGymId($gym_id, $plan_id);

        $startDate = Carbon::today();
        $data['member_id'] = $member->id;
        $data['plan_id'] = $plan->id;
        $data['start_date'] = $startDate->format('Y-m-d');
        $data['end_date'] = $startDate->copy()->addDays($plan->duration_days)->format('Y-m-d');
        return $this->subscriptionRepository->create($data);
    }


    public function renewSubscription($gym_id, $id, $plan_id, array $data)
    {

        $plan = $this->planRepository->findByGymId($gym_id, $plan_id);
        $subscription = $this->subscriptionRepository->findSubscription($gym_id, $id);


        $startDate = Carbon::today();
        if ($subscription->end_date && Carbon::parse($subscription->end_date)->isFuture()) {
            $startDate = Carbon::parse($subscription->end_date);
        }

        $data['plan_id'] = $plan->id;
        $data['start_date'] = $startDate->format('Y-m-d');
        $data['end_date'] = $startDate->copy()->addDays($plan->duration_days)->format('Y-m-d');


        return $this->subscriptionRepository->update($subscription, $data);
    }
}
