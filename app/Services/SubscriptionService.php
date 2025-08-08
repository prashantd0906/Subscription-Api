<?php
namespace App\Services;

use App\Interfaces\SubscriptionRepositoryInterface;

class SubscriptionService
{
    public function __construct(protected SubscriptionRepositoryInterface $repo) {}

    public function subscribe(int $userId, int $planId)
    {
        return $this->repo->subscribe($userId, $planId);
    }

    public function cancel(int $userId)
    {
        return $this->repo->cancel($userId);
    }

    public function getActive(int $userId)
    {
        return $this->repo->getActive($userId);
    }
}
