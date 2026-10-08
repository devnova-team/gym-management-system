<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\Subscription\StoreRequest;
use App\Http\Requests\Subscription\RenewRequest;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }



    public function index(Request $request)
    {
        $gym_id = $request->user()->gym_id;

        $subscriptions = $this->subscriptionService->getSubscriptions($gym_id);

        return ApiResponse::success([
            'subscriptions' => SubscriptionResource::collection($subscriptions)
        ], 'Subscriptions retrieved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $subscription = $this->subscriptionService->findSubscription($gym_id, $id);

        return ApiResponse::success([
            'subscription' => new SubscriptionResource($subscription)
        ], 'Subscription retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();
        $member_id = $data['member_id'];
        $plan_id = $data['plan_id'];

        $subscription = $this->subscriptionService->createSubscription($gym_id, $member_id, $plan_id, $data);

        return ApiResponse::success([
            'subscription' => new SubscriptionResource($subscription)
        ], 'Subscription was created successfully');
    }



    /**
     * Renew the specified resource in storage.
     */
    public function renew(RenewRequest $request)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();
        $plan_id = $data['plan_id'];


         $subscription = $this->subscriptionService->renewSubscription(
        $gym_id,
        $data['subscription_id'],
        $data['plan_id'],
        $data
    );
        return ApiResponse::success([
            'subscription' => new SubscriptionResource($subscription)
        ], 'Subscription renewed successfully');
    }
}
