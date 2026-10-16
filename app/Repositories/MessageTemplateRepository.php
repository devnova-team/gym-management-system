<?php

namespace App\Repositories;

use App\Models\MessageTemplate;
use Illuminate\Database\Eloquent\Collection;

class MessageTemplateRepository
{
    /**
     * Get all message templates for a specific gym.
     */
    public function getByGym(int $gymId): Collection
    {
        return MessageTemplate::query()
            ->where('gym_id', $gymId)
            ->orderByRaw(
                "FIELD(category, 'new', 'inactive', 'expiring', 'expired')"
            )
            ->get();
    }

    /**
     * Find a message template by gym and category.
     */
    public function findByGymAndCategory(
        int $gymId,
        string $category
    ): ?MessageTemplate {
        return MessageTemplate::query()
            ->where('gym_id', $gymId)
            ->where('category', $category)
            ->first();
    }

    /**
     * Update an existing message template.
     */
    public function updateTemplate(
        int $gymId,
        string $category,
        string $templateText
    ): ?MessageTemplate {
        $template = $this->findByGymAndCategory(
            $gymId,
            $category
        );

        if (!$template) {
            return null;
        }

        $template->update([
            'template_text' => $templateText,
        ]);

        return $template->refresh();
    }
}
