<?php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\PromoCodeRequest;
use App\Services\PromoCodeService;
use App\Helpers\ApiResponse;

class PromoCodeController extends Controller
{
    public function __construct(private readonly PromoCodeService $promoCodeService) {}

    public function store(PromoCodeRequest $request)
    {
        $promo = $this->promoCodeService->createPromoCode($request->validated());
        return ApiResponse::success($promo, 'Promo code created successfully.');
    }
}
