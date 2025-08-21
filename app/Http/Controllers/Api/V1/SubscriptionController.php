<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Http\Requests\Api\V1\CancelSubscriptionRequest;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $service) {}

    public function allPlans(): JsonResponse
    {
        return ApiResponse::success(
            $this->service->getAllPlans(),
            'All subscription plans retrieved successfully'
        );
    }

    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $result = $this->service->subscribe($request->validated());

        if (!($result['success'] ?? true)) {
            return ApiResponse::error($result['message'] ?? 'Subscription failed', 400);
        }

        return ApiResponse::success([
            'original_price'   => $result['original_price'] ?? null,
            'discount_applied' => $result['discount_applied'] ?? 0,
            'final_price'      => $result['final_price'] ?? null,
            'subscription'     => $result['subscription'] ?? null,
        ], 'Subscribed successfully');
    }

    public function cancel(CancelSubscriptionRequest $request): JsonResponse
{
    $userId = Auth::id();
    if (!$userId) {
        return ApiResponse::error('Unauthenticated', 401);
    }

    $planId = $request->validated()['plan_id'];   // Get plan_id from request

    $subscription = $this->service->cancel($userId, $planId);

    if (!$subscription) {
        return ApiResponse::error('No active subscription found for this plan', 404);
    }

    return ApiResponse::success(['subscription' => $subscription], 'Subscription cancelled successfully');
}


    public function active(): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return ApiResponse::error('Unauthenticated', 401);
        }

        $subscription = $this->service->getActive($userId);
        if (!$subscription) {
            return ApiResponse::error('No active subscription found', 404);
        }

        return ApiResponse::success(['subscription' => $subscription]);
    }
}
