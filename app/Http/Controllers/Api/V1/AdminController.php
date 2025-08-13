<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(protected SubscriptionPlanService $service) {}

    // Admin dashboard welcome
    public function dashboard(): JsonResponse
    {
        return response()->json(['message' => 'Welcome Admin!']);
    }

    // List all subscription plans
    public function index(): JsonResponse
    {
        $plans = $this->service->getAll();
        return response()->json(['data' => $plans]);
    }

    // Create a new subscription plan
    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->validated());
        return response()->json(['message' => 'Plan created successfully', 'data' => $plan]);
    }

    // Update an existing plan
    public function update(SubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->service->update($id, $request->validated());
        return response()->json(['message' => 'Plan updated successfully', 'data' => $plan]);
    }

    // Delete a plan
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Plan deleted successfully']);
    }

    // Fetch admin notifications
    public function notifications(): JsonResponse
    {
        $notifications = Auth::user()->notifications; // Only for authenticated admin
        return response()->json(['data' => $notifications]);
    }
}
