<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;

class ReportController extends Controller
{
    public function __construct(private readonly ReportRepositoryInterface $reportRepo) {}

    public function totalUsersPerPlan()
    {
        return response()->json($this->reportRepo->getTotalUserPlan());
    }

    public function activeSubscriptionsPerPlan()
    {
        return response()->json($this->reportRepo->getActiveSubscriptionsPerPlan());
    }

    public function monthlyNewSubscriptions()
    {
        return response()->json($this->reportRepo->getMonthlyNewSubscriptions());
    }

    public function planChurnRate()
    {
        return response()->json($this->reportRepo->getPlanChurnRate());
    }
}
