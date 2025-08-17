<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\PromoCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\PromoCodeRequest;
use App\Services\PromoCodeService;
use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

class PromoCodeController extends Controller
{
    public function __construct(private readonly PromoCodeService $promoCodeService) {}

    public function index(): JsonResponse
    {
        $promoCodes = PromoCode::where('valid_till', '>', now())->get();

        return ApiResponse::success($promoCodes, 'Valid promo codes retrieved successfully');
    }
    public function store(PromoCodeRequest $request)
    {
        $promo = $this->promoCodeService->createPromoCode($request->validated());
        return ApiResponse::success($promo, 'Promo code created successfully.');
    }
}
