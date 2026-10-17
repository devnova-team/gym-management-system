<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShiftService;
use App\Helpers\ApiResponse;
use App\Http\Resources\ShiftResource;
use App\Http\Requests\Shift\StoreRequest;

class ShiftController extends Controller
{

    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }


    public function index(Request $request)
    {

        $gym_id = $request->user()->gym_id;
        $shifts = $this->shiftService->getShifts($gym_id);

        return ApiResponse::success([
            'shifts' => ShiftResource::collection($shifts)
        ], 'Shifts retrieved successfully');
    }


    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();
        $staff_id = $data['staff_id'];

        $shift = $this->shiftService->createShift($gym_id, $staff_id, $data);

        return ApiResponse::success([
            'shift' => new ShiftResource($shift)
        ], 'Shift was created successfully');
    }
}
