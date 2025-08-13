<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct(protected SubscriptionPlanService $service) {}

    public function dashboard(): JsonResponse
    {
        return response()->json(['message' => 'Welcome Admin!']);
    }

    public function index(): JsonResponse
    {
        $plans = $this->service->getAll();
        return response()->json(['data' => $plans]);
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->validated());
        return response()->json(['message' => 'Plan created successfully', 'data' => $plan]);
    }

    public function update(SubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->service->update($id, $request->validated());
        return response()->json(['message' => 'Plan updated successfully', 'data' => $plan]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Plan deleted successfully']);
    }
}
