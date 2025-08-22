<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function summary()
    {
        return ApiResponse::success(
            $this->reportService->getSummary(),
            'Reports summary fetched successfully'
        );
    }
}
