<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\SubscriptionPromoCode;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscriptionPromoCodeRepository
{
    public function assign(int $subscriptionId, int $promoCodeId)
    {
        $subscription = Subscription::find($subscriptionId);

        if (!$subscription) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Subscription not found.'
            ], 404));
        }

        if ($subscription->status !== 'active') {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Promo code can only be applied to active subscriptions.'
            ], 400));
        }

        // Check if already assigned
        $subscriptionPromoCode = SubscriptionPromoCode::where('subscription_id', $subscriptionId)
            ->where('promo_code_id', $promoCodeId)
            ->first();

        if (!$subscriptionPromoCode) {
            $subscriptionPromoCode = SubscriptionPromoCode::create([
                'subscription_id' => $subscriptionId,
                'promo_code_id'   => $promoCodeId,
                'used_at'         => now(),
            ]);
        }

        $subscriptionPromoCode->load('promoCode');

        return $subscriptionPromoCode;
    }
}
