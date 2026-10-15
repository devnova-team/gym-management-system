<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\SegmentedMemberResource;
use App\Services\MemberSegmentationService;
use Illuminate\Http\Request;

class MemberSegmentationController extends Controller
{
    protected MemberSegmentationService $memberSegmentationService;

    public function __construct(
        MemberSegmentationService $memberSegmentationService
    ) {
        $this->memberSegmentationService = $memberSegmentationService;
    }

    public function index(Request $request)
    {
        $gymId = $request->user()->gym_id;

        $segments = $this->memberSegmentationService
            ->getSegmentedMembers($gymId);

        foreach ($segments as $category => $members) {
            $segments[$category] = collect($members)
                ->map(
                    fn ($member) => (new SegmentedMemberResource($member))
                        ->resolve($request)
                )
                ->values()
                ->all();
        }

        return ApiResponse::success(
            $segments,
            'Segmented members retrieved successfully.'
        );
    }
}
