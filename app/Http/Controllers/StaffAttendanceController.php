<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StaffAttendanceService;
use App\Http\Requests\Staff\AttendanceStoreRequest;
use App\Http\Resources\StaffAttendanceResource;
use App\Helpers\ApiResponse;

class StaffAttendanceController extends Controller
{

    protected $staffAttendanceService;

    public function __construct(StaffAttendanceService $staffAttendanceService)
    {
        $this->staffAttendanceService = $staffAttendanceService;
    }


    public function store(AttendanceStoreRequest $request)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();
        $staff_id = $data['staff_id'];

        $staffAttendance = $this->staffAttendanceService->storeAttendance($gym_id, $staff_id);

        return ApiResponse::success([
            'staffAttendance' => new StaffAttendanceResource($staffAttendance)
        ], 'Staff attendance stored successfully');
    }
}
