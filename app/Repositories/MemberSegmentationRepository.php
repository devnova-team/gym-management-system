<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;

class MemberSegmentationRepository
{
    /**
     * Get members belonging to a specific gym,
     * with their subscriptions, plans, and last attendance.
     */
    public function getMembersForSegmentation(int $gymId): Collection
    {
        return Member::query()
            ->where('gym_id', $gymId)
            ->with([
                  'gym',
                'subscriptions' => function ($query) use ($gymId) {
                    $query
                        ->whereHas('plan', function ($planQuery) use ($gymId) {
                            $planQuery->where('gym_id', $gymId);
                        })
                        ->with('plan');
                },
            ])
            ->withMax('attendance', 'check_in_time')
            ->get();
    }
}
