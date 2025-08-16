<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;

class AdminController extends Controller
{
    public function __construct(protected SubscriptionPlanService $service) {}

    public function dashboard(): JsonResponse
    {
        $admin = Auth::user();

        $notificationsCount = DatabaseNotification::where('notifiable_id', $admin->id)
            ->where('notifiable_type', User::class)
            ->count();

        return response()->json([
            'total_users'          => User::count(),
            'active_subscriptions' => Subscription::whereStatus('active')->count(),
            'total_plans'          => SubscriptionPlan::count(),
            'notifications_count'  => $notificationsCount,
            'message'              => 'Admin Dashboard Data'
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Plan created successfully',
            'data'    => $plan
        ]);
    }

    public function update(SubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->service->update($id, $request->validated());

        return response()->json([
            'message' => 'Plan updated successfully',
            'data'    => $plan
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Plan deleted successfully'
        ]);
    }
    
    public function notifications(): JsonResponse
    {
        $admin = Auth::user();

        $notifications = DatabaseNotification::where('notifiable_id', $admin->id)
            ->where('notifiable_type', User::class)
            ->latest()
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'data'  => $notifications
        ]);
    }
}
