<?php

namespace App\Services;

use App\Models\AdminReminder;
use App\Repositories\AdminReminderRepository;
use Illuminate\Database\Eloquent\Collection;

class AdminReminderService
{
    
    public function __construct(private readonly AdminReminderRepository $reminderRepository)
    {}

    public function create(array $data, int $gymId): AdminReminder
    {
        return $this->reminderRepository->create([
            'gym_id' => $gymId,
            'title' => $data['title'],
            'due_date' => $data['due_date'],
            'reminder_note' => $data['reminder_note'] ?? null,
        ]);
    }

    public function getReminders(
        int $gymId,
        ?string $status = null
    ): Collection {
        $today = now('Africa/Cairo')->toDateString();

        return $this->reminderRepository->getByGym(
            $gymId,
            $today,
            $status
        );
    }
}
