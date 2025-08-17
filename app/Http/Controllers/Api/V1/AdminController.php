<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;

class AdminController extends Controller
{
    public function __construct(protected SubscriptionPlanService $service) {}

    private function getAdminNotifications($admin)
    {
        return DatabaseNotification::where('notifiable_id', $admin->id)
            ->where('notifiable_type', User::class);
    }

    public function dashboard(): JsonResponse
    {
        $admin = Auth::user();

        $notificationsCount = $this->getAdminNotifications($admin)->count();

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
        return ApiResponse::success(
            $this->service->getAll(),
            'Fetched all subscription plans successfully'
        );
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->validated());

        return ApiResponse::success($plan, 'Plan created successfully');
    }

    public function update(SubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->service->update($id, $request->validated());

        return ApiResponse::success(
            $plan,
            'Plan updated successfully'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(
            null,
            'Plan deleted successfully'
        );
    }

    public function notifications(): JsonResponse
    {
        $admin = Auth::user();

        $notifications = $this->getAdminNotifications($admin)->latest()->get();

        return ApiResponse::success([
            'count' => $notifications->count(),
            'data'  => $notifications,
        ], 'Admin notifications fetched successfully');
    }
}
