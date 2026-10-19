<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function stats(Request $request)
    {
        $gymId = (int) $request->user()->gym_id;

        $stats = $this->dashboardService->getStats($gymId);

        return (new DashboardResource($stats))
            ->additional([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully.',
            ]);
    }
}
