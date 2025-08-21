<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Interfaces\SubscriptionRepositoryInterface;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    
    public function getAll()
    {
        return SubscriptionPlan::select('id', 'name', 'price', 'duration')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function subscribe(int $userId, int $planId): Subscription
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $now = now();

        $this->cancelActiveSubscription($userId);   // Cancel any active subscription before creating new one

        return Subscription::create([
            'user_id'       => $userId,
            'plan_id'       => $planId,
            'plan_duration' => $plan->duration ?? 30,
            'start_date'    => $now,
            'end_date'      => $now->copy()->addDays($plan->duration ?? 30),
            'status'        => 'active',
        ]);
    }

    public function create(array $data)
    {
        return Subscription::create($data);
    }

    public function cancel(int $userId, int $planId): ?Subscription
    {
        $subscription = Subscription::where('user_id', $userId)
            ->where('plan_id', $planId)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $subscription->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'end_date'     => now()->addDays($subscription->plan_duration),
            ]);
        }

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
        $active = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($active) {
            $active->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
                'end_date'     => now()->addDays($active->plan_duration),
            ]);
        }
    }
}
