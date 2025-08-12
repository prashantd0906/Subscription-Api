<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $service) {}

    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $subscription = $this->service->subscribe($request->validated());

        return response()->json([
            'message' => 'Subscribed successfully',
            'data' => $subscription
        ]);
    }

    public function cancel(): JsonResponse
    {
        $userId = Auth::id(); // Using facade here
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $this->service->cancel($userId);

        return response()->json(['message' => 'Subscription cancelled']);
    }

    public function active(): JsonResponse
    {
        $userId = Auth::id(); // Using facade here
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $subscription = $this->service->getActive($userId);

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        return response()->json(['data' => $subscription]);
    }
}
