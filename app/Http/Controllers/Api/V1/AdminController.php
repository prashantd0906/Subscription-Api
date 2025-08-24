<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Notification;


class AdminController extends Controller
{
    public function __construct(
        protected SubscriptionPlanService $service,
        protected UserActivityService $activityService
    ) {}

    public function dashboard(): JsonResponse
    {
        $notificationsCount = $this->activityService       // count all subscription activity
            ->getUserActivities()
            ->whereIn('action', ['subscription_started', 'subscription_cancelled'])
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

        if (!$plan) {
            return ApiResponse::error("No plan found with id {$id}", 404);
        }

        return ApiResponse::success($plan, 'Plan updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $plan = $this->service->find($id);

        if (!$plan) {
            return ApiResponse::error('', 404, "Plan with ID {$id} not found");
        }

        $this->service->delete($id);

        return ApiResponse::success('', 'Plan deleted successfully');
    }

    public function notifications(): JsonResponse
    {
        $notifications = Notification::latest()->get();

        return ApiResponse::success($notifications, 'Notifications retrieved successfully');
    }
}
