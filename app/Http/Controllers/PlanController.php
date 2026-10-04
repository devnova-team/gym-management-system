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

    protected $PlanService;

    public function __construct(PlanService $PlanService)
    {
        $this->PlanService = $PlanService;
    }



    public function index(Request $request)
    {
        $gym_id = $request->user()->gym_id;

        $Plans = $this->PlanService->getPlans($gym_id);

        return ApiResponse::success([
            'Plans' => PlanResource::collection($Plans)
        ], 'Plans retrieved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $Plan = $this->PlanService->getPlanById($gym_id, $id);

        return ApiResponse::success([
            'Plan' => new PlanResource($Plan)
        ], 'Plan retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $Plan = $this->PlanService->createPlan($gym_id, $data);

        return ApiResponse::success([
            'Plan' => new PlanResource($Plan)
        ], 'Plan was created successfully');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $Plan = $this->PlanService->updatePlan($gym_id, $id, $data);

        return ApiResponse::success([
            'Plan' => new PlanResource($Plan)
        ], 'Plan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $this->PlanService->deletePlan($gym_id, $id);

        return ApiResponse::success(null, 'Plan deleted successfully');
    }
}
