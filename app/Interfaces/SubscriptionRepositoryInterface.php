<?php

namespace App\Interfaces;

use App\Models\Subscription;

interface SubscriptionRepositoryInterface
{
    public function create(array $data);
    public function subscribe(int $userId, int $planId):Subscription;
    public function cancel(int $userId);
    public function getActive(int $userId);
    public function cancelActiveSubscription(int $userId): void;
}
