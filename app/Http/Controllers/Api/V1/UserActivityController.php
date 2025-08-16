<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UserActivityController extends Controller
{
    public function __construct(protected UserActivityService $activityService) {}

    // Fetch authenticated user's activity
    public function myActivity(): JsonResponse
    {
        $activities = $this->activityService->getUserActivities(Auth::id());
        return response()->json(['data' => $activities]);
    }

    // For admins: fetch all users' activities
    public function allActivities(): JsonResponse
    {
        $activities = $this->activityService->getUserActivities(null); 
        return response()->json(['data' => $activities]);
    }
}
