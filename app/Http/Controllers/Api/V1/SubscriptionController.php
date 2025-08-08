<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Services\SubscriptionService;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $service,
        protected UserActivityService $activityService
    ) {}

    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $subscription = $this->service->subscribe($userId, $request->plan_id);

        $this->activityService->log(
            $userId,
            'subscribe',
            'User subscribed to plan ID ' . $request->plan_id
        );

        return response()->json([
            'message' => 'Subscribed successfully',
            'data' => $subscription
        ]);
    }

    public function cancel(): JsonResponse
    {
        $userId = Auth::id();
        $this->service->cancel($userId);

        $this->activityService->log(
            $userId,
            'cancel',
            'User cancelled their subscription'
        );

        return response()->json(['message' => 'Subscription cancelled']);
    }

    public function active(): JsonResponse
    {
        $userId = Auth::id();
        $subscription = $this->service->getActive($userId);

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        return response()->json(['data' => $subscription]);
    }
}
