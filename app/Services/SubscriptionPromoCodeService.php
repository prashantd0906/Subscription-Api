<?php

namespace App\Services;

use App\Repositories\SubscriptionPromoCodeRepository;

class SubscriptionPromoCodeService
{
    public function __construct(
        private readonly SubscriptionPromoCodeRepository $repository
    ) {}

    public function assignPromoCode(int $subscriptionId, int $promoCodeId)
    {
        return $this->repository->assign($subscriptionId, $promoCodeId);
    }
}
