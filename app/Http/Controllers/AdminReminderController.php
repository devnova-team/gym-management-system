<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reminder\StoreReminderRequest;
use App\Http\Resources\AdminReminderResource;
use App\Services\AdminReminderService;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class AdminReminderController extends Controller
{
     public function __construct(
        private readonly AdminReminderService $reminderService
    ) {}
    
     public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:upcoming,due'],
        ]);

        $gymId = (int) $request->user()->gym_id;

        $reminders = $this->reminderService->getReminders(
            $gymId,
            $validated['status'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminders retrieved successfully.',
            'data' => [
                'reminders' => AdminReminderResource::collection($reminders),
            ],
        ]);
    }

    public function store(StoreReminderRequest $request): JsonResponse
    {
        $gymId = (int) $request->user()->gym_id;

        $reminder = $this->reminderService->create(
            $request->validated(),
            $gymId
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminder created successfully.',
            'data' => [
                'reminder' => new AdminReminderResource($reminder),
            ],
        ], 201);
    }
    
}
