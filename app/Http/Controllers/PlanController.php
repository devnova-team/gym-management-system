<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Plan\StoreRequest;
use App\Http\Requests\Plan\UpdateRequest;
use App\Services\PlanService;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }



    public function index(Request $request)
    {
        $gym_id = $request->user()->gym_id;

        $plans = $this->planService->getPlans($gym_id);

        return ApiResponse::success([
            'plans' => PlanResource::collection($plans)
        ], 'Plans retrieved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $plan = $this->planService->getPlanById($gym_id, $id);

        return ApiResponse::success([
            'plan' => new PlanResource($plan)
        ], 'Plan retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $plan = $this->planService->createPlan($gym_id, $data);

        return ApiResponse::success([
            'plan' => new PlanResource($plan)
        ], 'Plan was created successfully');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $plan = $this->planService->updatePlan($gym_id, $id, $data);

        return ApiResponse::success([
            'plan' => new PlanResource($plan)
        ], 'Plan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $this->planService->deletePlan($gym_id, $id);

        return ApiResponse::success(null, 'Plan deleted successfully');
    }
}
