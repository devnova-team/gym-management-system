<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Financial\FinancialSummaryRequest;
use App\Http\Resources\FinancialSummaryResource;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
     public function __construct(
        protected FinancialReportService $financialReportService
    ) {}
    
    public function summary(
        FinancialSummaryRequest $request
    ): JsonResponse {

        $gymId = (int) $request->user()->gym_id;

        $data = $request->validated();

        $summary = $this->financialReportService
            ->getFinancialSummary(
                $gymId,
                $data['from'],
                $data['to']
            );

        return ApiResponse::success(
            (new FinancialSummaryResource($summary))->resolve($request),
            'Financial summary retrieved successfully.'
        );
    }
}
