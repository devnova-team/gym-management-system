<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AttendanceResource;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
     protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

     public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer', 'min:1'],
        ]);

        $gymId = (int) $request->user()->gym_id;

        $attendance = $this->attendanceService->checkIn(
            $gymId,
            (int) $validated['member_id']
        );

        $attendance->load('member');

        return ApiResponse::success([
            'attendance' => new AttendanceResource($attendance),
        ], 'Member checked in successfully.', 201);
    }
       
     public function checkOut(Request $request, int $id)
    {
        $gymId = (int) $request->user()->gym_id;

        $attendance = $this->attendanceService->checkOut(
            $gymId,
            $id
        );

        return ApiResponse::success([
            'attendance' => new AttendanceResource($attendance),
        ], 'Member checked out successfully.');
    }
     
    public function today(Request $request)
    {
        $gymId = (int) $request->user()->gym_id;

        $attendances = $this->attendanceService->getToday($gymId);

        return ApiResponse::success([
            'attendances' => AttendanceResource::collection(
                $attendances->getCollection()
            ),
            'pagination' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
        ], 'Today attendance retrieved successfully.');
    }


    public function currentlyInside(Request $request)
    {
        $gymId = (int) $request->user()->gym_id;

        $attendances = $this->attendanceService
            ->getCurrentlyInside($gymId);

        return ApiResponse::success([
            'attendances' => AttendanceResource::collection(
                $attendances->getCollection()
            ),
            'pagination' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
        ], 'Currently inside members retrieved successfully.');
    }

}