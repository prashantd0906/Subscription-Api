<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UserActivityController extends Controller
{
    public function __construct(protected UserActivityService $activityService) {}

    public function myActivity(): JsonResponse    // Fetch authenticated user's activity
    {
        $activities = $this->activityService->getUserActivities(Auth::id());
        return response()->json(['data' => $activities]);
    }

    public function allActivities(): JsonResponse   // For admins: fetch all users' activities
    {
        $activities = $this->activityService->getUserActivities(null);
        return response()->json(['data' => $activities]);
    }
}
