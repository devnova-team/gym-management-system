<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\MessageTemplates\UpdateMessageTemplateRequest;
use App\Http\Resources\MessageTemplateResource;
use App\Services\MessageTemplateService;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
      protected MessageTemplateService $messageTemplateService;

    public function __construct(
        MessageTemplateService $messageTemplateService
    ) {
        $this->messageTemplateService = $messageTemplateService;
    }

    /**
     * Get all message templates for the authenticated gym.
     */
    public function index(Request $request)
    {
        $gym_id = $request->user()->gym_id;

        $templates = $this->messageTemplateService
            ->getTemplates($gym_id);

        return ApiResponse::success([
            'templates' => MessageTemplateResource::collection($templates),
        ], 'Message templates retrieved successfully');
    }

    /**
     * Update a message template.
     */
    public function update(UpdateMessageTemplateRequest $request)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();

        $template = $this->messageTemplateService->updateTemplate(
            $gym_id,
            $data['category'],
            $data['template_text']
        );

        return ApiResponse::success([
            'template' => new MessageTemplateResource($template),
        ], 'Message template updated successfully');
    }
}
