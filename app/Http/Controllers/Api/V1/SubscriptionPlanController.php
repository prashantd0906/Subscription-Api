<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller
{
    public function __construct(protected SubscriptionPlanService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated()));
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->service->find($id));
    }

    public function update(SubscriptionPlanRequest $request, $id): JsonResponse
    {
        return response()->json($this->service->update($id, $request->validated()));
    }

    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
