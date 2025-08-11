<?php

namespace App\Repositories;

use App\Models\SubscriptionPromoCode;

class SubscriptionPromoCodeRepository
{
    public function assign(int $subscriptionId, int $promoCodeId)
    {
        return SubscriptionPromoCode::create([
            'subscription_id' => $subscriptionId,
            'promo_code_id' => $promoCodeId,
        ]);
    }
}
