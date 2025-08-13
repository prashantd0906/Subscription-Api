<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    public function __construct(protected UserActivityService $service) {}

    // For a user to see their own activities
    public function myActivity(): JsonResponse
    {
        $logs = $this->service->getUserActivities(Auth::id());
        return response()->json(['data' => $logs]);
    }

    // For admin to see all activities (with optional filters)
    public function allActivities(Request $request): JsonResponse
    {
        $logs = $this->service->getAllActivities(
            $request->query('action'),
            $request->query('start_date'),
            $request->query('end_date')
        );

        return response()->json(['data' => $logs]);
    }
}
