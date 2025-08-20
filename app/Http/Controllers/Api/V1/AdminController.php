<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;

class AdminController extends Controller
{
    public function __construct(
        protected SubscriptionPlanService $service,
        protected UserActivityService $activityService
    ) {}

    /**
     * Fetch all admin notifications
     */
    public function notifications(): JsonResponse
    {
        $admin = Auth::user();

        // Fetch latest notifications
        $notifications = $admin->notifications()->latest()->get();

        // Transform for cleaner JSON
        $data = $notifications->map(function ($notification) {
            return [
                'id'              => $notification->id,
                'subscriber_name' => $notification->data['subscriber_name'] ?? null,
                'plan_name'       => $notification->data['plan_name'] ?? null,
                'action'          => $notification->data['action'] ?? null,
                'timestamp'       => $notification->data['timestamp'] ?? null,
                'created_at'      => $notification->created_at->toDateTimeString(),
                'read_at'         => $notification->read_at?->toDateTimeString(),
            ];
        });

        return ApiResponse::success([
            'count' => $data->count(),
            'data'  => $data,
        ], 'Admin notifications fetched successfully');
    }

    /**
     * Dashboard stats for admin
     */
    public function dashboard(): JsonResponse
    {
        // Count all subscription-related activities
        $notificationsCount = $this->activityService
            ->getAll()
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

    /**
     * Manage subscription plans
     */
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

        return ApiResponse::success($plan, 'Plan updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Plan deleted successfully');
    }
}
