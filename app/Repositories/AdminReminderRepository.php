<?php

namespace App\Repositories;

use App\Models\AdminReminder;
use Illuminate\Database\Eloquent\Collection;

class AdminReminderRepository
{
    public function create(array $data): AdminReminder
    {
        return AdminReminder::create($data);
    }

    public function getByGym(
        int $gymId,
        string $today,
        ?string $status = null
    ): Collection {
        $query = AdminReminder::query()
            ->where('gym_id', $gymId);

        if ($status === 'upcoming') {
            $query->whereDate('due_date', '>', $today);
        }

        if ($status === 'due') {
            $query->whereDate('due_date', '<=', $today);
        }

        return $query
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();
    }
}
