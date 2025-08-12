<?php

namespace App\Repositories;

use App\Models\SubscriptionPromoCode;

class SubscriptionPromoCodeRepository
{
    public function assign(int $subscriptionId, int $promoCodeId): SubscriptionPromoCode
    {
        // Create record
        $subscriptionPromoCode = SubscriptionPromoCode::create([
            'subscription_id' => $subscriptionId,
            'promo_code_id'   => $promoCodeId,
            'applied_at'      => now(),
        ]);

        // Load promo code relationship
        $subscriptionPromoCode->load('promoCode');

        return $subscriptionPromoCode;
    }
}
