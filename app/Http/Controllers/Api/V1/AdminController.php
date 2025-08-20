<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;

class AdminController extends Controller
{
    public function __construct(
        protected SubscriptionPlanService $service
    ) {}

    public function dashboard(): JsonResponse
    {
        $admin = auth()->user();

        return response()->json([
            'total_users'          => User::count(),
            'active_subscriptions' => Subscription::whereStatus('active')->count(),
            'total_plans'          => SubscriptionPlan::count(),
            'notifications_count'  => $admin->notifications()->count(),
            'message'              => 'Admin Dashboard Data'
        ]);
    }

    public function notifications(): JsonResponse
    {
        $admin = auth()->user();
        $notifications = $admin->notifications()->latest()->get();

        return ApiResponse::success([
            'count' => $notifications->count(),
            'data'  => $notifications->map(fn($n) => [
                'id'         => $n->id,
                'type'       => class_basename($n->type),
                'data'       => $n->data,
                'read_at'    => $n->read_at?->toDateTimeString(),
                'created_at' => $n->created_at->toDateTimeString(),
            ]),
        ], 'Admin notifications fetched successfully');
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
        return ApiResponse::success($plan, 'Plan updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return ApiResponse::success(null, 'Plan deleted successfully');
    }
}
