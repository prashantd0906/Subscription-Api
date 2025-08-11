<?php
namespace App\Repositories;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Interfaces\SubscriptionRepositoryInterface;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function subscribe(int $userId, int $planId):Subscription
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $now = now();

        $this->cancelActiveSubscription($userId);

        return Subscription::create([
            'user_id'    => $userId,
            'plan_id'    => $planId,
            'start_date' => $now,
            'end_date'   => $now->copy()->addDays($plan->duration),
            'status'     => 'active',
        ]);
    }

    // New method: subscription with custom data
    public function create(array $data)
    {
        return Subscription::create($data);
    }

    public function cancel(int $userId)
    {
        $subscription = $this->getActive($userId);

        if (!$subscription) {
            throw new \Exception('No active subscription found.');
        }

        $subscription->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return $subscription;
    }

    public function getActive(int $userId)
    {
        return Subscription::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }

    public function cancelActiveSubscription(int $userId): void
    {
        Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
    }
}
