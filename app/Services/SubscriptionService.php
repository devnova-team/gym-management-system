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
    return \Illuminate\Support\Facades\DB::transaction(function () use (
        $gym_id,
        $member_id,
        $plan_id,
        $data
    ) {
        // Lock member to prevent concurrent subscription creation.
        $member = $this->subscriptionRepository
            ->findMemberForUpdate($gym_id, $member_id);

        // Ensure the plan belongs to the same gym.
        $plan = $this->planRepository
            ->findByGymId($gym_id, $plan_id);

        $today = Carbon::today('Africa/Cairo');

        // Prevent creating another subscription while a paid
        // current or future subscription already exists.
        $hasExistingSubscription = $member->subscriptions()
            ->where('payment_status', 'paid')
            ->whereDate('end_date', '>=', $today->toDateString())
            ->exists();

        if ($hasExistingSubscription) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'member_id' => [
                    'The member already has a current or future paid subscription. Use renewal instead.',
                ],
            ]);
        }

        $subscriptionData = [
            'member_id' => $member->id,
            'plan_id' => $plan->id,
            'start_date' => $today->toDateString(),
            'end_date' => $today->copy()
                ->addDays($plan->duration_days - 1)
                ->toDateString(),
            'payment_status' => $data['payment_status'],
            'payment_method' => $data['payment_method'],
            'amount_paid' => $data['payment_status'] === 'paid'
        ? $plan->price
        : null,
    'paid_at' => $data['payment_status'] === 'paid'
        ? now('UTC')
        : null,
        ];

        return $this->subscriptionRepository
            ->create($subscriptionData)
            ->load(['member', 'plan']);
    });
}


      public function renewSubscription($gym_id, $id, $plan_id, array $data)
{
    return \Illuminate\Support\Facades\DB::transaction(
        function () use ($gym_id, $id, $plan_id, $data) {

            $subscription = $this->subscriptionRepository
                ->findSubscription($gym_id, $id);

            // Lock member to serialize subscription operations.
            $member = $this->subscriptionRepository
                ->findMemberForUpdate(
                    $gym_id,
                    $subscription->member_id
                );

            // Reload after acquiring the lock.
            $subscription = $this->subscriptionRepository
                ->findSubscription($gym_id, $id);

            $plan = $this->planRepository
                ->findByGymId($gym_id, $plan_id);

            if ($subscription->payment_status !== 'paid') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subscription_id' => [
                        'Only paid subscriptions can be renewed.',
                    ],
                ]);
            }

            // Do not renew an older subscription.
            $latest = $this->subscriptionRepository
                ->getLatestMemberSubscription($gym_id, $member->id);

            if ($latest && $latest->id !== $subscription->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subscription_id' => [
                        'This is not the latest subscription for this member.',
                    ],
                ]);
            }

            $today = Carbon::today('Africa/Cairo');
            $startDate = $today->copy();

            if ($subscription->end_date->gte($today)) {

                $isExhausted = false;

                if ($subscription->plan->type === 'limited') {
                    $usedSessions = $this->subscriptionRepository
                        ->countUsedSessions($subscription->id);

                    $isExhausted = $usedSessions >=
                        $subscription->plan->total_sessions;
                }

                if (! $isExhausted) {
                    $startDate = $subscription->end_date
                        ->copy()
                        ->addDay();
                }
            }

            $newSubscription = [
                'member_id' => $member->id,
                'plan_id' => $plan->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $startDate->copy()
                    ->addDays($plan->duration_days - 1)
                    ->toDateString(),
                'payment_status' => $data['payment_status'],
                'payment_method' => $data['payment_method'],
                'amount_paid' => $data['payment_status'] === 'paid'
                    ? $plan->price
                    : null,

                'paid_at' => $data['payment_status'] === 'paid'
                    ? now('UTC')
                    : null,
            ];

            return $this->subscriptionRepository
                ->create($newSubscription)
                ->load(['member', 'plan']);
        }
    );
}
}
