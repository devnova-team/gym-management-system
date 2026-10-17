<?php

namespace App\Services;

use App\Models\MessageTemplate;
use App\Repositories\MessageTemplateRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class MessageTemplateService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MessageTemplateRepository $messageTemplateRepository
    )  { } 

    /**
     * Get all message templates for a gym.
     */
    public function getTemplates(int $gymId): Collection
    {
        return $this->messageTemplateRepository->getByGym($gymId);
    }

     /**
     * Update an existing template for a specific gym and category.
     */
    public function updateTemplate(
        int $gymId,
        string $category,
        string $templateText
    ): MessageTemplate {
        $template = $this->messageTemplateRepository->updateTemplate(
            $gymId,
            $category,
            $templateText
        );

        if ($template === null) {
            throw ValidationException::withMessages([
                'category' => [
                    'No message template exists for this category.',
                ],
            ]);
        }

        return $template;
    }
}



