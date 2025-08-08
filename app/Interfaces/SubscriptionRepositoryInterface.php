<?php
namespace App\Interfaces;

interface SubscriptionRepositoryInterface
{
    public function subscribe(int $userId, int $planId);
    public function cancel(int $userId);
    public function getActive(int $userId);
}
