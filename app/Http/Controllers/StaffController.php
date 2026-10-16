<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StaffService;
use App\Helpers\ApiResponse;
use App\Http\Resources\StaffResource;
use App\Http\Requests\Staff\StoreRequest;

class StaffController extends Controller
{

    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }


    public function index(Request $request)
    {

        $gym_id = $request->user()->gym_id;
        $staff = $this->staffService->getStaff($gym_id);

        return ApiResponse::success([
            'shifts' => StaffResource::collection($staff)
        ], 'Staff retrieved successfully');
    }


    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();

        $staff = $this->staffService->createStaff($gym_id, $data);

        return ApiResponse::success([
            'staff' => new StaffResource($staff)
        ], 'Staff was created successfully');
    }
}
