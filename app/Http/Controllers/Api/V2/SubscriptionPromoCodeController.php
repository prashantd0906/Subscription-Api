<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\AssignPromoCodeRequest;
use App\Services\SubscriptionPromoCodeService;

class SubscriptionPromoCodeController extends Controller
{
    public function __construct(
        private readonly SubscriptionPromoCodeService $service
    ) {}

    public function assign(AssignPromoCodeRequest $request)
    {
        $result = $this->service->assignPromoCode(
            $request->validated()['subscription_id'],
            $request->validated()['promo_code_id']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Promo code assigned to subscription successfully.',
            'data'    => [
                'subscription_id' => $result->subscription_id,
                'promo_code_id'   => $result->promo_code_id,
                'discount'        => $result->promoCode->discount ?? null,
                'valid_till'      => $result->promoCode->valid_till ?? null,
                'applied_at'      => $result->applied_at
            ]
        ]);
    }
}
